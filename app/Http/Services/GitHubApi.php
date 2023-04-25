<?php

namespace App\Http\Services;

use App\Models\Mod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class GitHubApi
 * @package App\Http\Services
 */
class GitHubApi
{
    /**
     * @return array
     */
    public static function fetchRepos()
    {
        $user = Auth::user();
        $retRepos = [];

        $organizations = Http::asForm()->withHeaders([
            "Accept" => "application/json",
            "Authorization" => "Bearer  " . $user->tokens->where("type", "github")->last()->access
        ])->get($user->githubData->organizations_url);
        $organizations = json_decode($organizations->body());

        foreach ($organizations as $org) {
            $orgRepos = Http::asForm()->withHeaders([
                "Accept" => "application/json",
                "Authorization" => "Bearer  " . $user->tokens->where("type", "github")->last()->access
            ])->get($org->repos_url);
            $orgRepos = json_decode($orgRepos->body());

            foreach ($orgRepos as $or)
                if ($or->permissions->admin) //Only get organization repos if the user is listed as an admin
                    $retRepos[] = [
                        "id" => $or->id,
                        "name" => $or->full_name,
                        "url" => $or->html_url,
                    ];
        }

        $repos = Http::asForm()->withHeaders([
            "Accept" => "application/json",
            "Authorization" => "Bearer  " . $user->tokens->where("type", "github")->last()->access
        ])->get($user->githubData->repos_url);
        $repos = json_decode($repos->body());

        foreach ($repos as $repo)
            $retRepos[] = [
                "id" => $repo->id,
                "name" => $repo->full_name,
                "url" => $repo->html_url,
            ];

        return $retRepos;
    }

    /**
     * @param Mod $mod
     * @return mixed
     */
    public static function fetchRepo(Mod $mod)
    {
        $user = Auth::user();
        $repo = Http::asForm()->withHeaders([
            "Accept" => "application/json",
            "Authorization" => "Bearer  " . $user->tokens->where("type", "github")->last()->access
        ])->get("https://api.github.com/repositories/" . $mod->repo_id);

        return json_decode($repo->body());
    }

    /**
     * @param $repo
     * @param $path
     * @return mixed
     */
    public static function fetchRepoDirectories($repo, $path = '')
    {
        $user = Auth::user();
        $endpoint = "https://api.github.com/repos/" . $repo . "/contents/" . $path;

        $repo = Http::asForm()->withHeaders([
            "Accept" => "application/json",
            "Authorization" => "Bearer  " . $user->tokens->where("type", "github")->last()->access
        ])->get($endpoint);

        return json_decode($repo->body());
    }
}

