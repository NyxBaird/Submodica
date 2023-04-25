<?php
namespace App\Http\Controllers;

use App\Http\Services\BunnyApi;
use App\Http\Services\GitHubApi;
use App\Http\Services\Respond;
use App\Libraries\BunnyCDN\BunnyCDN;
use App\Mail\Report;
use App\Models\DevLog;
use App\Models\Mod;
use App\Models\ModDownload;
use App\Models\ModImage;
use App\Models\ModReport;
use App\Models\ModStatisticsDownloadsUnique;
use App\Models\ModStatisticsLikes;
use App\Models\ModStatisticsViewsUnique;
use App\Models\Tag;
use App\Models\User;
use Google\Cloud\Vision\V1\Image;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\Datatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\ImageManagerStatic;

/**
 * Class ModController
 * @package App\Http\Controllers
 */
class ModController extends Controller
{
    /**
     * @var string
     */
    private $failedToSaveMessage = "Failed to save! Please save your mod details in a text file and send them to an Administrator for analysis.";
    /**
     * @var BunnyCDN
     */
    private $cdn;
    /**
     * @var int
     */
    private $requiredModManagementLevel = 90;

    public function __construct()
    {
        parent::__construct();
        $this->cdn = new BunnyCDN('submodica', env('BUNNY_KEY'), 'la');
    }

    /**
     * @param Request $request
     * @param $game
     * @return Respond::class
     */
    public function create(Request $request, $game)
    {
        if (!$request->post('terms', false))
            Respond::error("You must read and agree to the rules & terms of service to create a mod.");

        //This is a workaround for some of the complicated tiptap data found in $request->post('description')
        $description = json_encode(json_decode($request->getContent())->description);

        $mod = new Mod([
            'user_id' => Auth::user()->id,
            'creator' => $request->post('creator'),
            'title' => $request->post('title'),
            'game' => $game, //sbz or sn1
            'description' => $description,
            'repo_id' => $request->post('repo_id'),
            'creation_status' => 'index',
            'tagline' => $request->post('tagline'),
            'subnautica_compatibility' => $request->post('subnautica_compatibility'),
        ]);

        if (!$mod->save())
            Respond::error($this->failedToSaveMessage);

        $mod->updateStatus('index');

        foreach ($request->post('tags') as $tag) {
            $tag = strtolower(is_string($tag) ? $tag : $tag['tag']);
            if ($model = Tag::where(DB::raw('LOWER(tag)'), $tag)->first())
                $mod->tags()->attach($model->id);
            else
                $mod->tags()->create([
                    'tag' => $tag
                ])->save();
        }

        Respond::success("Success!", $mod);
    }

    /**
     * @param $game
     * @param Mod $mod
     * @param Request $request
     * @return Respond::class
     */
    public function edit($game, Mod $mod, Request $request)
    {
        if (Auth::user()->id !== $mod->user_id && Auth::user()->role->level < $this->requiredModManagementLevel)
            Respond::error("You do not have permission to edit this mod.");

        //This is a workaround for some of the complicated tiptap data found in $request->post('description')
        $data = $request->post();
        $data['description'] = json_encode(json_decode($request->getContent())->description);

        /*
         * Handle all our tag stuff
         */
        $currentTagIds = $mod->tags->map(function ($tag) { return $tag->id; })->toArray();
        foreach ($data['tags'] as $tag) {

            if (is_string($tag)) {
                $tag = strtolower($tag);
                if ($model = Tag::where(DB::raw('LOWER(tag)'), $tag)->first())
                    $mod->tags()->attach($model->id);
                else
                    $mod->tags()->create([
                        'mod_id' => $mod->id,
                        'tag' => $tag
                    ])->save();

                continue;
            } else if (is_array($tag) && !isset($tag['pivot'])) {
                $mod->tags()->attach($tag['id']);
            }

            //Mark old tags as still existing
            if (isset($tag['id']) && ($key = array_search($tag['id'], $currentTagIds)) !== false)
                unset($currentTagIds[$key]);
        }

        //Delete old tags that didn't make it
        foreach ($currentTagIds as $id) {
            $tag = Tag::where('id', $id)->first();
            if ($tag && !$tag->mods->except($mod->id)->count())
                $tag->delete();
            else
                $mod->tags()->detach($id);
        }

        $mod->update($data);

        if ($mod->save())
            Respond::success("Success!", $mod);
        else
            Respond::error($this->failedToSaveMessage);
    }

    public function duplicate($game, Mod $mod, Request $request)
    {
        if (Auth::user()->id !== $mod->user_id && Auth::user()->role->level < $this->requiredModManagementLevel)
            Respond::error("You do not have permission to duplicate this mod.");

        $newMod = $mod->replicate();

        $updated = $request->post();
        $updated['views'] = 0;
        $updated['downloads'] = 0;
        $updated['creation_status'] = 'index';
        $updated['description'] = json_encode(json_decode($request->getContent())->description);
        $newMod->fill($updated);

        if (!$newMod->save())
            Respond::error($this->failedToSaveMessage);

        //Duplicate Tag Entries
        foreach ($request->post('tags') as $tag) {
            if (is_string($tag)) {
                $tag = strtolower($tag);
                if ($model = Tag::where(DB::raw('LOWER(tag)'), $tag)->first())
                    $newMod->tags()->attach($model->id);
                else
                    $newMod->tags()->create([
                        'mod_id' => $newMod->id,
                        'tag' => $tag
                    ])->save();

                continue;
            } else if (is_array($tag) && !isset($tag['pivot'])) {
                $newMod->tags()->attach($tag['id']);
            }
        }

        //Duplicate Attributions
        foreach ($mod->attributions as $attribution) {
            $attribution = $attribution->replicate();
            $attribution->mod_id = $newMod->id;

            $newMod->attributions()->create($attribution->toArray())->save();
        }

        //Duplicate Downloads
        foreach ($mod->downloadLinks as $download) {
            $download = $download->replicate();
            $download->mod_id = $newMod->id;

            $newMod->downloadLinks()->create($download->toArray())->save();
        }

        //Duplicate Image Entries
        foreach ($mod->images as $image) {
            $image = $image->replicate();
            $image->mod_id = $newMod->id;

            $newMod->images()->create($image->toArray())->save();
        }

        Respond::success("Success!", $newMod);
    }

    /**
     * @param $game
     * @param Mod $mod
     * @param Request $request
     * @return Respond::class
     */
    public function saveAttributes($game, Mod $mod, Request $request)
    {
        if (Auth::user()->id !== $mod->user_id && Auth::user()->role->level < $this->requiredModManagementLevel)
            Respond::error("You do not have permission to edit this mod.");

        $mod->attributions()->delete();

        foreach ($request->post('local') as $local)
            $mod->attributions()->create([
                'mod_id' => $mod->id,
                'local_attribution_id' => $local['code']
            ])->save();

        foreach ($request->post('custom') as $custom)
            $mod->attributions()->create([
                'mod_id' => $mod->id,
                'name' => $custom['name'],
                'url' => $custom['url']
            ])->save();

        $mod->misc_attributions = $request->post('additionalInfo');
        $mod->save();

        $mod->updateStatus('attributions');

        Respond::success("Success!");
    }

    /**
     * @param $game
     * @param Mod $mod
     * @param Request $request
     * @return Respond::class
     */
    public function saveDownloads($game, Mod $mod, Request $request) {
        if (Auth::user()->id !== $mod->user_id && Auth::user()->role->level < $this->requiredModManagementLevel)
            Respond::error("You do not have permission to edit this mod.");

        $currentDownloadIds = $mod->downloadLinks->map(function ($link) { return $link->id; })->toArray();
        $downloads = $request->post('downloads');

        //determine which downloads need to be deleted
        foreach ($downloads as $download) {
            if (isset($download['id'])) {
                if (in_array($download['id'], $currentDownloadIds)) {
                    $key = array_search($download['id'], $currentDownloadIds);
                    unset($currentDownloadIds[$key]);
                }
            }
        }
        //whatevers left here needs to get deleted
        foreach ($currentDownloadIds as $id)
            $mod->downloadLinks()->where('id', $id)->delete();

        foreach ($downloads as $download) {
            $download['title'] = isset($download['title']) && $download['title'] ? $download['title'] : null;

            if (isset($download['id'])) {
                if (in_array($download['id'], $currentDownloadIds)) {
                    $key = array_search($download['id'], $currentDownloadIds);
                    unset($currentDownloadIds[$key]);
                }

                $mod->downloadLinks()->where('id', $download['id'])->update([
                    'version' => $download['version'],
                    'url' => $download['url'],
                    'checksum' => md5_file($download['url']),
                    'title' => $download['title']
                ]);
                continue;
            }

            if (!$download['title'] && DB::table('mod_downloads')->where('version', $download['version'])->where('mod_id', $mod->id)->count())
                Respond::error("You may not have multiple download links with the same version number.");

            try {
                $mod->downloadLinks()->create([
                    'mod_id' => $mod->id,
                    'version' => $download['version'],
                    'url' => $download['url'],
                    'checksum' => md5_file($download['url']),
                    'title' => $download['title']
                ])->save();
            } catch (\ErrorException $e) {
                Respond::error("We were unable to verify the file at \"{$download['url']}\". Please ensure it's a downloadable release file from this mods github repository to save.");
            }
        }

        if ($cv = $request->input('currentVersion', false)) {
            $mod->latest_version = $cv;
            $mod->save();
        }

        $mod->show_cover_title = $request->post('show_cover_title', false);
        $mod->save();

        $mod->updateStatus('downloads');

        Respond::success("Download links updated successfully!");
    }

    /**
     * @param $game
     * @param Mod $mod
     * @param Request $request
     * @return Respond::class
     * @throws \App\Libraries\BunnyCDN\BunnyCDNStorageException
     * @throws \Google\ApiCore\ApiException
     * @throws \Google\ApiCore\ValidationException
     */
    public function saveImage($game, Mod $mod, Request $request) {
        if (Auth::user()->id !== $mod->user_id && Auth::user()->role->level < $this->requiredModManagementLevel)
            Respond::error("You do not have permission to edit this mod.");

        $maxImagesPerMod = 10;
        if ($mod->id === 208)
            $maxImagesPerMod = 23;

        if ($mod->images->where('type', 'gallery')->count() >= $maxImagesPerMod)
            Respond::error("You may not have more than $maxImagesPerMod images in your gallery.");

        //wip double check image sizes
        //wip check google cloud vision api usage
        //wip check image count

        if (!str_contains($request->post('image'), 'data:image/png;base64'))
            Respond::success('Already uploaded image');

        //Authenticate with Google Cloud
        $client = new ImageAnnotatorClient([
            'credentials' => json_decode(file_get_contents(base_path('submodica-722e98a3a1fb.json')), true)
        ]);


        $base64Data = str_replace("data:image/png;base64,", "", $request->post('image'));
//            dd($i);

        //To start make sure our image passes google's safe search
        $image = new Image();
        $image->setContent(base64_decode($base64Data));
        $result = $client->safeSearchDetection($image);
        if ($result->getError()) {
            DevLog::create([
                'key' => 'vision-api-error',
                'data' => json_encode(["message" => $result->getError()->getMessage(), "data" => substr($base64Data, 0, 100)])
            ])->save();

            Respond::error("We failed to screen an image for appropriateness. Please try again with a different image or else contact Nyx if you believe this to be in error.");
        }

        $annotations = $result->getSafeSearchAnnotation();

        $modImage = new ModImage([
            'mod_id' => $mod->id,
            'source' => "",
            'type' => $request->post('type'),
            'adult' => $annotations->getAdult(),
            'spoof' => $annotations->getSpoof(),
            'medical' => $annotations->getMedical(),
            'violence' => $annotations->getViolence(),
            'racy' => $annotations->getRacy()
        ]);

        $limit = 3;
        if ($modImage->adult > $limit
            || $modImage->medical > $limit
            || $modImage->violence > ($limit + 1)
            || $modImage->racy > $limit) {

            Auth::user()->flags()->create([
                'user_id' => Auth::user()->id,
                'type' => 'strike',
                'reason' => "User uploaded an image that was deemed inappropriate by Google's Vision API. " . json_encode($modImage->toArray())
            ])->save();

            Respond::error("Your image was deemed inappropriate by an AI and your account has been flagged for review. Please try again with a different image or else contact Nyx if you believe this to be in error.");
        }

        $filename = $mod->id . "-" . strtotime('now') . ".webp";
        $tempPath = storage_path('app/public/images/mods/' . $filename);

        ImageManagerStatic::configure(['driver' => 'imagick']);
        $img = ImageManagerStatic::make(base64_decode($base64Data));
        $img->encode('webp', 100)->save($tempPath);

        $response = $this->cdn->uploadFile($tempPath, '/submodica/mods/' . $filename);

        File::delete($tempPath);

        if ($response->HttpCode != "201") {
            DevLog::create([
                'key' => 'cdn-upload-error',
                'data' => $response
            ])->save();

            Respond::error("We failed to upload your image to our CDN. Please refresh the page and try again or contact Nyx.");
        }

        $modImage->source = 'https://submodica.b-cdn.net/mods/' . $filename;
        $modImage->save();

        $mod->updateStatus('downloads');

        Respond::success("Your image was successfully uploaded!");
    }

    /**
     * @param $game
     * @param Mod $mod
     * @param Request $request
     * @return Respond::class
     */
    public function updatePreviewStatus($game, Mod $mod, Request $request)
    {
        if (Auth::user()->id !== $mod->user_id && Auth::user()->role->level < $this->requiredModManagementLevel)
            Respond::error("You do not have permission to edit this mod.");

        $repo = GitHubApi::fetchRepo($mod);
        if (!isset($repo->html_url))
            Respond::error("There was an issue verifying your mod's GitHub repository. Please try again later or contact Nyx for support.");

        $mod->repo_url = $repo->html_url;

        $mod->donation_link = $request->post('mod')['donation_link'];
        $mod->show_donation_link = isset($request->post('mod')["show_donation_link"]) && $request->post('mod')["show_donation_link"];
        $mod->save();

        if ($request->post('publish'))
            $mod->updateStatus('live');
        else
            $mod->updateStatus('preview');

        Respond::success("Your mod was " . ($request->post('publish') ? 'published' : 'saved') . "!");
    }

    /**
     * @param ModDownload $download
     * @return Respond::class
     *
     * If we decide to track by IP we'll probably need to update our Privacy Policy
     */
    public function trackDownload(ModDownload $download)
    {
        $msg = "Your download should begin shortly!";

        if (!Auth::user()) {
            //Count all unidentified downloads
            $download->mod->downloads += 1;
            $download->mod->save();

            Respond::success($msg);
        } else {
            //If the mod author tries to download, let them but don't count it
            if (Auth::user()->id == $download->mod->user_id)
                Respond::success($msg);

            //If our user has already downloaded this mod just update their version
            if ($stat = ModStatisticsDownloadsUnique::where('mod_id', $download->mod->id)->where('user_id', Auth::user()->id)->first()) {
                $stat->version = $download->version;
                $stat->save();
                Respond::success($msg);
            }

            //This user hasn't downloaded this mod before so track it
            $download->mod->downloads += 1;
            $download->mod->save();

            $stat = new ModStatisticsDownloadsUnique([
                'mod_id' => $download->mod->id,
                'user_id' => Auth::user()->id,
                'version' => $download->version
            ]);
            $stat->save();
            Respond::success($msg);
        }
    }

    /**
     * @param Mod $mod
     * @return Respond::class
     */
    public function trackView(Mod $mod)
    {
        $msg = 'View counted';
        if (!Auth::user()) {
            //Count all unidentified views
            $mod->views += 1;
            $mod->save();

            Respond::success($msg);
        } else {
            //If the mod author tries to download, let them but don't count it
            if (Auth::user()->id == $mod->user_id)
                Respond::success($msg);

            //If our user has already downloaded this mod just update their version
            if ($stat = ModStatisticsViewsUnique::where('mod_id', $mod->id)->where('user_id', Auth::user()->id)->first()) {
                $stat->touch(); //This updates the updated_at time
                Respond::success($msg);
            }

            //This user hasn't downloaded this mod before so track it
            $mod->views += 1;
            $mod->save();

            $stat = new ModStatisticsViewsUnique([
                'mod_id' => $mod->id,
                'user_id' => Auth::user()->id
            ]);
            $stat->save();

            Respond::success($msg);
        }
    }

    /**
     * @param $game
     * @param Mod $mod
     * @param Request $request
     * @return Respond::class
     */
    public function report(Mod $mod, Request $request)
    {
        if (!Auth::user())
            Respond::error("You must be logged in to report a mod.");

        if (!$request->post('reason', false) || !$request->post('details', false))
            Respond::error("Please provide a reason and details for your report.");

        $report = ModReport::create([
            'mod_id' => $mod->id,
            'reporter_id' => Auth::user()->id,
            'reason' => $request->post('reason'),
            'details' => $request->post('details')
        ]);
        $report->save();

        Mail::to('admin@submodica.xyz')->send(new Report($report));

        Respond::success("Your report has been submitted! An admin will review it and take action as necessary.");
    }

    public function favorite(Mod $mod)
    {
        if (!Auth::user())
            Respond::error("You must be logged in to favorite a mod.");

        $favorite = ModStatisticsLikes::where('user_id', Auth::user()->id)->where('mod_id', $mod->id)->first();
        if ($favorite) {
            $favorite->delete();
            Respond::success("Removed mod from favorites!", ['favorite' => false]);
        } else {
            $favorite = new ModStatisticsLikes([
                'user_id' => Auth::user()->id,
                'mod_id' => $mod->id
            ]);
            $favorite->save();
            Respond::success("Added mod to favorites!", ['favorite' => true]);
        }
    }

    /**
     * @param $game
     * @param Mod $mod
     * @param Request $request
     * @return Respond::class
     */
    public function delete($game, Mod $mod, Request $request)
    {
        if (Auth::user()->id !== $mod->user_id && Auth::user()->role->level < $this->requiredModManagementLevel)
            Respond::error("You do not have permission to edit this mod.");

        $game = explode("/", $request->getRequestUri())[2];
        if (Auth::user()->id !== $mod->user_id && Auth::user()->role->level < $this->requiredModManagementLevel)
            Respond::error("You do not have permission to delete this mod.", [], "/mods/$game/manage");

        $mod->delete();
        Respond::success("Success!", [], "/mods/$game/manage");
    }

    /**
     * @param Mod $mod
     * @param ModImage $image
     * @return Respond::class
     */
    public function deleteImage(Mod $mod, ModImage $image)
    {
        if (Auth::user()->id !== $mod->user_id && Auth::user()->role->level < $this->requiredModManagementLevel)
            Respond::error("You do not have permission to edit this mod.");

        $imageIsDuplicate = ModImage::where('source', $image->source)->count() > 1;

        $response = false;
        if (!$imageIsDuplicate)
            $response = $this->cdn->deleteObject("/submodica".str_replace("https://submodica.b-cdn.net", "", $image->source));

        if ($imageIsDuplicate || (!$imageIsDuplicate && $response && $response->HttpCode == 200)) {
            $image->deleteLocalOnly();

            $addendum = "";
            if (in_array($image->type, ['icon', 'cover']) && $mod->creation_status === 'live') {
                $image->mod->creation_status = 'downloads';
                $image->mod->save();

                $addendum .= ", mod was unpublished until a suitable replacement is uploaded";
            }

            Respond::success("Image deleted successfully$addendum!");
        }

        Respond::error("Failed to delete image. Please try again or contact Nyx.");
    }

    /**
     * Fetches things
     */
    public function fetch(Mod $mod)
    {
        if ($mod->creation_status !== 'live' && (Auth::user()->id !== $mod->user_id && Auth::user()->role->level < $this->requiredModManagementLevel))
            Respond::error("You do not have permission to edit this mod.");

        Respond::success("Success!", ["mod" => $mod, 'tags' => $mod->tags]);
    }

    /**
     * @param $game
     * @param Mod $mod
     * @return Respond::class
     */
    public function fetchAttributions($game, Mod $mod)
    {
        if ($mod->creation_status !== 'live' && (Auth::user()->id !== $mod->user_id && Auth::user()->role->level < $this->requiredModManagementLevel))
            Respond::error("You do not have permission to edit this mod.");

        Respond::success("Success!", [
            "attributions" => $mod->attributions()->with('attributedMod')->get(),
            "additionalInfo" => $mod->misc_attributions
        ]);
    }

    /**
     * @param Mod $mod
     * @return Respond::class
     */
    public function fetchRepo(Mod $mod)
    {
        if ($mod->creation_status !== 'live' && (Auth::user()->id !== $mod->user_id && Auth::user()->role->level < $this->requiredModManagementLevel))
            Respond::error("You do not have permission to edit this mod.");

        Respond::success("Success!", GitHubApi::fetchRepo($mod));
    }

    /**
     * @param Mod $mod
     * @return Respond::class
     */
    public function fetchDownloads(Mod $mod)
    {
        if ($mod->creation_status !== 'live' && (Auth::user()->id !== $mod->user_id && Auth::user()->role->level < $this->requiredModManagementLevel))
            Respond::error("You do not have permission to edit this mod.");

        Respond::success("Success!", $mod->downloadLinks);
    }

    /**
     * @param Mod $mod
     * @return Respond::class
     */
    public function fetchImages(Mod $mod)
    {
        if ($mod->creation_status !== 'live' && (Auth::user()->id !== $mod->user_id && Auth::user()->role->level < $this->requiredModManagementLevel))
            Respond::error("You do not have permission to edit this mod.");

        Respond::success("Success!", $mod->images);
    }

    /**
     * @param Mod $mod
     * @return Respond::class
     */
    public function fetchWithAll(Mod $mod)
    {
        $data = Mod::with('tags', 'images', 'downloadLinks')->find($mod->id)->toArray();
        $data['attributions'] = $mod->attributions()->with('attributedMod')->get();
        $data['maintainer'] = $mod->user->name;
        $data['isFavorite'] = Auth::user() && ModStatisticsLikes::where('mod_id', $mod->id)->where('user_id', Auth::user()->id)->exists();

        if (in_array($mod->user_id, User::getBannedIds()))
            Respond::error("This mod is owned by a banned user and cannot be viewed.");

        Respond::success("Success!", $data);
    }

    /**
     * @param $game
     * @param Request $request
     * @return Respond::class
     *
     * This is used for the Attributions page Submodica mod select search
     */
    public function search($game, Request $request)
    {
        if ($request->get('q')) {
            $query = Mod::where('game', $game)->select('id AS code', DB::raw("CONCAT(title, ' by ', creator) as label"));
            $query->where('creation_status', 'live')
                ->whereNotIn("mods.user_id", User::getBannedIds())
                ->where(function($innerQuery) use ($request) {
                    $innerQuery->where('title', 'like', "%{$request->get('q')}%");
                    $innerQuery->orWhere('creator', 'like', "%{$request->get('q')}%");
                    $innerQuery->orWhereHas('tags', function ($q) use ($request) {
                        $q->where('tag', 'like', "%{$request->get('q')}%");
                    });
                });
        } else
            $query = Mod::where('game', $game)->select('id AS code', DB::raw("CONCAT(title, ' by ', creator) as label"))->orderBy('title');

        $mods = $query->limit(30)->get()->toArray();

        Respond::success("Success!", $mods);
    }

    /*
     * Datatable functions below here
     */

    /**
     * @param $game
     * @param Request $request
     * @return Respond::class
     */
    public function list($game, Request $request)
    {
        $compatibility = $request->post('searchPrefix', '');
        $mods = Mod::query()->select(
            'mods.id',
                'mods.latest_version',
                'mods.title',
                'mods.creator',
                'mods.tagline',
                'mods.views',
                'mods.downloads',
                'mods.subnautica_compatibility',
                DB::raw('(SELECT source FROM mod_images WHERE mod_id=mods.id and type="icon" LIMIT 1) AS profile_image'),
                DB::raw("(SELECT COUNT(id) FROM mod_statistics_likes WHERE mod_id=mods.id) AS likes"),
                'mods.created_at',
                DB::raw("(SELECT created_at FROM mod_downloads WHERE mod_id=mods.id AND version=mods.latest_version ORDER BY created_at DESC LIMIT 1) AS updated_at")
            )
            ->whereNotIn("mods.user_id", User::getBannedIds())
            ->where('mods.game', $game)
            ->where('mods.creation_status', 'live');

        if ($compatibility && $game === 'sn1')
            $mods->where('mods.subnautica_compatibility', $compatibility);

        $mods->groupBy('id');

        Respond::success("Success!", Datatable::process($mods, $request->post(), ['tags']));
    }


    /**
     * This returns all of a users mods for their manage page
     * @param $game
     * @param Request $request
     * @return Respond::class
     */
    public function manageList($game, Request $request)
    {
        $mods = Mod::where('game', $game)->where('user_id', Auth::user()->id);
        Respond::success("Success!", Datatable::process($mods, $request->post()));
    }
    public function favoritesList($game, Request $request)
    {
        $mods = Mod::query()->select(
                'mods.*',
                DB::raw('(SELECT source FROM mod_images WHERE mod_id=mods.id and type="icon" LIMIT 1) AS profile_image'),
                DB::raw("(SELECT COUNT(id) FROM mod_statistics_likes WHERE mod_id=mods.id) AS likes")
            )
            ->whereNotIn("mods.user_id", User::getBannedIds())
            ->where('mods.game', $game)
            ->where('mods.creation_status', 'live')
            ->whereHas('modStatisticLikes', function ($q) {
                $q->where('user_id', Auth::user()->id);
            });

        $mods->groupBy('id');

        Respond::success("Success!", Datatable::process($mods, $request->post(), ['tags']));
    }

    public function getTrending()
    {
        $mods = Mod::where('creation_status', 'live')->orderBy('downloads', 'desc')->limit(10)->with('images')->get();

        Respond::success("Success!", $mods);
    }
}
