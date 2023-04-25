<?php
namespace App\Http\Controllers;

use App\Http\Services\BunnyApi;
use App\Http\Services\Respond;
use App\Mail\Report;
use App\Mail\ReportComment;
use App\Models\Mod;
use App\Models\ModComment;
use App\Models\ModCommentReport;
use App\Models\ModReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * Class ModController
 * @package App\Http\Controllers
 */
class ModCommentsController extends Controller
{
    private $requiredCommentManagementLevel = 90;

    public function create(Mod $mod, ModComment $comment = null, Request $request)
    {
        $data = $request->post();

        if (!isset($data['id']))
            $data['id'] = 0;

        $data['parent_id'] = $comment ? $comment->id : null;
        $data['user_id'] = Auth::user()->id;
        $data['mod_id'] = $mod->id;
        $data['comment'] = json_encode(json_decode($request->getContent())->comment);

        if (!$data['id']) {
            unset($data['id']);
            $comment = ModComment::create($data);

            if (!$comment->save())
                Respond::error("Failed to save comment. Please refresh the page and try again.");
        } else {
            $comment = ModComment::where('id', $data['id'])->first();
            if (!$comment)
                Respond::error("Comment not found.");

            if (Auth::user()->id !== $comment->user_id && Auth::user()->role->level < $this->requiredCommentManagementLevel)
                Respond::error("You do not have permission to edit this comment.");

            //For some reason we have the wrong parent id here. Easy answer? Since this is an edit just ignore it. Comments will never change parent
            unset($data['parent_id']);

            $comment->update($data);
            if (!$comment->save())
                Respond::error("Failed to save comment. Please refresh the page and try again.");
        }

        Respond::success("Success!", $comment);
    }

    /**
     * @param $game
     * @param Mod $mod
     * @param Request $request
     * @return Respond::class
     */
    public function delete(Mod $mod, ModComment $comment, Request $request)
    {
        if (Auth::user()->id !== $comment->user_id && Auth::user()->role->level < $this->requiredCommentManagementLevel)
            Respond::error("You do not have permission to delete this comment.");

        if (!$comment->delete())
            Respond::error("Failed to delete comment. Please refresh the page and try again.");

        Respond::success("Success!");
    }

    public function fetch(Mod $mod, ModComment $comment = null)
    {
        if ($mod->creation_status !== 'live' && (Auth::user()->id !== $mod->user_id && Auth::user()->role->level < $this->requiredModManagementLevel))
            Respond::error("You do not have permission to fetch comments for this mod.");

        $comments = [];

        if (!$comment)
            $collection = ModComment::where('mod_id', $mod->id)->where('parent_id', null);
        else
            $collection = ModComment::where('parent_id', $comment->id);

        $collection
            ->where('deleted', false)
            ->orderBy('pinned', 'desc')
            ->orderBy('created_at', 'asc')
            ->with('author')->get()
            ->each(function ($comment, $key) use ($mod, &$comments) {
                $author = User::find($comment->user_id);

                if ($author->id === $mod->user_id)
                    $avatar = $author->githubData->avatar_url;
                else {
                    $dd = $author->discordData;
                    $avatar = "https://cdn.discordapp.com/avatars/" . $dd->discord_id . "/" . $dd->avatar;
                }

                $comments[$key] = $comment->toArray();
                $comments[$key]['author'] = [
                    'id' => $author->id,
                    'name' => $author->name,
                    'avatar' => $avatar,
                    'role' => $author->role->name,
                    'role_color' => $author->role->color,
                    'role_level' => $author->role->level,
                ];
            });

        Respond::success("Success!", $comments);
    }

    public function report(Mod $mod, ModComment $comment, Request $request)
    {
        if (!Auth::user())
            Respond::error("You must be logged in to report a mod.");

        if (!$request->post('reason', false) || !$request->post('details', false))
            Respond::error("Please provide a reason and details for your report.");

        $report = ModCommentReport::create([
            'comment_id' => $comment->id,
            'reporter_id' => Auth::user()->id,
            'reason' => $request->post('reason'),
            'details' => $request->post('details')
        ]);
        $report->save();

        Mail::to('admin@submodica.xyz')->send(new ReportComment($report));

        Respond::success("Your report has been submitted! An admin will review it and take action as necessary.");
    }

    public function toggleLock(Mod $mod, ModComment $comment) {
        if (Auth::user()->id !== $comment->user_id && Auth::user()->role->level < $this->requiredCommentManagementLevel)
            Respond::error("You do not have permission to lock this comment.");

        if ($comment->locked)
            $comment->locked = false;
        else
            $comment->locked = true;

        $comment->save();
        Respond::success("Comment ".($comment->locked?"locked":"un-locked")."!", ['locked' => $comment->locked]);
    }

    public function togglePin(Mod $mod, ModComment $comment) {
        if (Auth::user()->id !== $comment->user_id && Auth::user()->role->level < $this->requiredCommentManagementLevel)
            Respond::error("You do not have permission to pin this comment.");

        if ($comment->pinned)
            $comment->pinned = false;
        else
            $comment->pinned = true;

        $comment->save();
        Respond::success("Comment ".($comment->pinned?"pinned":"un-pinned")." (it won't appear in its new location until you refresh your page)!", ['pinned' => $comment->pinned]);
    }
}
