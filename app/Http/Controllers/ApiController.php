<?php
namespace App\Http\Controllers;

use App\Http\Services\GitHubApi;
use App\Http\Services\Respond;
use App\Libraries\BunnyCDN\BunnyCDN;
use App\Models\Mod;
use App\Models\ModDownload;
use App\Models\ModImage;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Class ApiController
 * @package App\Http\Controllers
 */
class ApiController extends Controller
{
    private $cdn;
    private $githubApi;

    public function __construct()
    {
        if (request()->is('api/search/*')
            || request()->is('api/getVersions')
            || request()->is('api/recordGuid')
            || request()->is('api/getByGuid')
        ) {

            $token = request()->input('token', false) ?: (isset(request()->route()->parameters()['token']) ? request()->route()->parameters()['token'] : false);
            if (!$token || $token !== env("LEE_TOKEN")) //This token belongs to Lee23
                Respond::error("Invalid API token");
        } else
            $this->middleware('auth');

        $this->githubApi = new GitHubApi();
        $this->cdn = new BunnyCDN('submodica', env('BUNNY_KEY'), 'la');
    }

    /**
     * @return void
     */
    public function fetchRepos()
    {
        Respond::success("Success!", $this->githubApi->fetchRepos());
    }

    public function fetchRepoDirectories(Request $request)
    {
        Respond::success("Success!", $this->githubApi->fetchRepoDirectories($request->post("repo"), $request->post("path", "")));
    }

    public function searchTags(Request $request)
    {
        if ($request->get('q'))
            $query = Tag::where(DB::raw('LOWER(tag)'), 'LIKE', '%' . strtolower($request->get('q')) . '%')->where('tag', '!=', $request->get('q'));
        else
            $query = Tag::query();

        Respond::success("Success!", $query->limit(30)->get());
    }

    public function fetchAllImages()
    {
        if (!Auth::user() || Auth::user()->role->level <= 90)
            return Respond::error("You do not have permission to do this!");

        $pathObjects = $this->cdn->getStorageObjects('/submodica/mods');

        $return = [];
        foreach ($pathObjects as $file) {
            $local = ModImage::where('source', 'like', "%{$file->ObjectName}%")->with(['mod', 'mod.user'])->first();
            $return[] = [
                'name' => $file->ObjectName,
                'exists' => $local ?: false,
                'created_at' => date("Y-m-d h:ia", strtotime($file->DateCreated)),
                'updated_at' => date("Y-m-d H:i", strtotime($file->LastChanged))
            ];
        }

        $return = collect($return)->sortByDesc('created_at')->values()->all();

        Respond::success("Success!", $return);
    }

    public function deleteImage(ModImage $image)
    {
        if (!Auth::user() || Auth::user()->role->level <= 90)
            return Respond::error("You do not have permission to do this!");

        $response = $this->cdn->deleteObject("/submodica".str_replace("https://submodica.b-cdn.net", "", $image->source));
        if ($response->HttpCode != 200) {
            Respond::success("There was a problem deleting this image! Please refresh the page and try again.");
        }

        $addendum = "";
        if (in_array($image->type, ['icon', 'cover']) && $image->mod->creation_status === 'live') {
            $image->mod->creation_status = 'downloads';
            $image->mod->save();

            $addendum .= ", mod was unpublished until a suitable replacement is uploaded";
        }

        $image->delete();
        Respond::success("Image deleted$addendum.");
    }

    public function verifyDiscordServerMembership()
    {
        if (!Auth::user())
            return Respond::error("You need to be logged in for this!");

        //we may need to request additional access for this

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token->access])->get('https://discord.com/api/users/@me');
        $userData = json_decode($response->body());
    }

    public function search($game, $token, $query = false)
    {
        $mods = Mod::query()->select(
            'mods.id AS id',
            DB::raw("CONCAT('https://submodica.xyz/mods/$game/', mods.id) as url"),
            DB::raw('(SELECT source FROM mod_images WHERE mod_id=mods.id and type="icon" LIMIT 1) AS profile_image'),
            'mods.title',
            'mods.creator',
            'mods.tagline',
            'mods.views',
            'mods.downloads',
            DB::raw("(SELECT COUNT(id) FROM mod_statistics_likes WHERE mod_id=mods.id) AS likes"),
            'mods.latest_version',
            'mods.subnautica_compatibility',
            'mods.created_at',
            DB::raw("(SELECT created_at FROM mod_downloads WHERE mod_id=mods.id AND version=mods.latest_version ORDER BY created_at DESC LIMIT 1) AS updated_at"),
        )
            ->join('mod_downloads', 'mods.id', '=', 'mod_downloads.mod_id')
            ->whereNotIn("mods.user_id", User::getBannedIds())
            ->where('mods.game', $game)
            ->where('mods.subnautica_compatibility', '2.0')
            ->where('mods.creation_status', 'live');

        $mods->groupBy('id');

        switch ($query) {
            case 'recently_updated':
                $mods->orderBy('updated_at', 'DESC');
                break;

            default:
                $mods->where(function ($q) use ($query) {
                    $q->where('mods.title', 'LIKE', "%$query%")
                        ->orWhere('mods.creator', 'LIKE', "%$query%")
                        ->orWhereHas('tags', function ($q) use ($query) {
                            $q->where('tag', 'like', "%{$query}%");
                        });
                });
                break;
        }
        $mods->orderBy('mods.downloads', 'DESC');
        $mods->limit(100);

        Respond::success("Success!", $mods->get());
    }

    public function getVersions(Request $request) {
        $mods = $request->input('mods');
        $return = [];
        foreach ($mods as $mod_id) {
            $mod = Mod::find($mod_id);
            $return[strval($mod_id)] = $mod->latest_version;
        }

        Respond::success("Success!", $return);
    }

    public function recordGuid(Request $request)
    {
        $downloads = ModDownload::where('checksum', $request->input('checksum'))->get();
        $downloads->each(function($download) use ($request) {
            $download->guid = $request->input('guid');
            $download->save();
        });
        Respond::success('Success!');
    }

    public function getByGuid(Request $request)
    {
        $mod = Mod::query()->select(
            'mods.id AS id',
            DB::raw("CONCAT('https://submodica.xyz/mods/', mods.game, '/', mods.id) as url"),
            DB::raw('(SELECT source FROM mod_images WHERE mod_id=mods.id and type="icon" LIMIT 1) AS profile_image'),
            'mods.title',
            'mods.creator',
            'mods.tagline',
            'mods.views',
            'mods.downloads',
            DB::raw("(SELECT COUNT(id) FROM mod_statistics_likes WHERE mod_id=mods.id) AS likes"),
            'mods.latest_version',
            'mods.subnautica_compatibility',
            'mods.created_at',
            DB::raw("(SELECT created_at FROM mod_downloads WHERE mod_id=mods.id AND version=mods.latest_version ORDER BY created_at DESC LIMIT 1) AS updated_at"),
        )
            ->join('mod_downloads', 'mods.id', '=', 'mod_downloads.mod_id')
            ->whereNotIn("mods.user_id", User::getBannedIds())
            ->where('mods.creation_status', 'live')
            ->where('mod_downloads.guid', $request->input('guid'));

        $mod->groupBy('id');

        Respond::success("Success!", $mod->get());
    }
}
