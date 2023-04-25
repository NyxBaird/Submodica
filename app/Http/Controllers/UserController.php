<?php
namespace App\Http\Controllers;

use App\Http\Services\Datatable;
use App\Http\Services\Respond;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * @return void
     */
    public function getConnectionInfo() {
        $discord = Auth::user()->discordData;
        $github = Auth::user()->githubData;

        if ($discord)
            $discord = [
                "username" => $discord->username . "<small>#" . $discord->discriminator . "</small>",
                "email" => $discord->email,
                "avatar" => "https://cdn.discordapp.com/avatars/" . $discord->discord_id . "/" . $discord->avatar
            ];

        if ($github)
            $github = [
                "username" => $github->login,
                "email" => $github->email,
                "avatar" => $github->avatar_url
            ];

        Respond::success("", [
            "discord" => $discord,
            "github" => $github
        ]);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function fetchManageable(Request $request) {
        $user = Auth::user();
        $mods = User::whereHas('role', function ($q) use ($user) {
            $q->where('level', '<=', $user->role->level);
        });

        Respond::success("Success!", Datatable::process($mods, $request->post()));
    }

    /**
     * @param User $user
     * @return void
     */
    public function ban(User $user) {
        if (Auth::user()->role->level < Role::where("name", "admin")->first()->level)
            Respond::error("You do not have permission to do this!", [], '/staff/users');

        if (Auth::user()->role->level <= $user->role->level)
            Respond::error("You cannot ban this user!", [], '/staff/users');

        if ($user->github_id==10285054)
            Respond::error("You cannot ban this account!", [], '/staff/users');

        if (!$user->banned)
            $user->banned = true;
        else
            $user->banned = false;

        $user->save();

        Respond::success("Success!", $user, "/staff/users");
    }

    public function updateProfile(Request $request) {
        $user = Auth::user();
        $user->name = $request->post("name");
        $user->donation_link = $request->post("donation_link");
        $user->save();

        Respond::success("Success!", $user);
    }
}
