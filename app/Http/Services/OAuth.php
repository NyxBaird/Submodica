<?php

namespace App\Http\Services;

use App\Models\GithubData;
use App\Models\User;
use App\Models\Token;
use App\Models\DiscordData;
use App\Models\UserMeta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

/**
 * Class OAuthService
 * @package App\Http\Services
 */
class OAuth
{
    /**
     * @var Token
     */
    private Token $token;

    /**
     * @var
     */
    private $user;

    /**
     * @var bool
     */
    private $authForExistingUser = false;

    /**
     * @param Token $token
     */
    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    /**
     * We should always return from this service provider with this message
     *
     * @param $success
     * @param $message
     * @param $user
     * @return array
     */
    private function respond($success, $message, $user = false) {
        return ["success" => $success, "message" => $message, "user" => $user];
    }

    /**
     * @param $type
     * @param $tokenData
     * @param $user
     * @return array|false|void
     */
    public function saveTokenData($type, $tokenData)
    {
        $this->user = null;
        if ($this->user = Auth::user()) {
            if (!$this->user->getMeta("AuthForExisting", false))
                Respond::error("Please use your Profile page to connect to " . ucfirst($type), [], "/users/profile");
            else {
                $this->user->deleteMeta("AuthForExisting");
                $this->authForExistingUser = true;
            }
        }

        $oldToken = Token::where("access", $tokenData->access_token)->first();
        if ($oldToken) {
            if ($this->user && ($oldToken->user && $oldToken->user != $this->user))
                return $this->respond(false, "There was an issue authenticating you. Please make sure you are signed out.");

            $oldToken->delete();
        }

        $this->token->user_id = $this->user?->id;
        $this->token->type = $type;
        $this->token->access = (string)$tokenData->access_token;
        $this->token->scope = $tokenData->scope;

        if ($type == "discord") {
            $this->token->refresh = (string)$tokenData->refresh_token;
            $this->token->expires = date("Y-m-d H:i:s", ($tokenData->expires_in + strtotime('now')));

            //This function returns a welcome message as well as saves updates to our token
            return $this->updateDiscordInfo();
        } else if ($type == "github") {
            $this->token->refresh = "";
            $this->token->expires = date("Y-m-d H:i:s", strtotime('now'));

            return $this->updateGithubInfo();
        }
    }

    /**
     * @return array
     */
    private function updateGithubInfo()
    {
        $userData = Http::asForm()->withHeaders([
            "Accept" => "application/json",
            "Authorization" => "Bearer  " . $this->token->access
        ])->get("https://api.github.com/user");

        $emailRequest = Http::asForm()->withHeaders([
            "Accept" => "application/json",
            "Authorization" => "Bearer  " . $this->token->access
        ])->get("https://api.github.com/user/emails");

        $userData = json_decode($userData->body());

        $emailList = [];
        $email = $userData->login . strtotime("now") . "@placeholder.com";
        if ($emailRequest->ok()) {
            foreach(json_decode($emailRequest->body()) as $e) {
                $emailList[] = $e->email;
                if ($e->primary == true) {
                    if ($e->verified = true)
                        $email = $e->email;
                    else
                        return $this->respond(false, "You must validate your primary Github email before you can sign in with it.");
                }
            }
        }

        $githubData = GithubData::where("github_id", $userData->id)->firstOrCreate([
            'login' => $userData->login,
            'github_id' => $userData->id,
            'node_id' => $userData->node_id,
            'avatar_url' => $userData->avatar_url,
            'gravatar_id' => $userData->gravatar_id ?: "",
            'url' => $userData->url,
            'html_url' => $userData->html_url,
            'followers_url' => $userData->followers_url,
            'following_url' => $userData->following_url,
            'gists_url' => $userData->gists_url,
            'starred_url' => $userData->starred_url,
            'subscriptions_url' => $userData->subscriptions_url,
            'organizations_url' => $userData->organizations_url,
            'repos_url' => $userData->repos_url,
            'events_url' => $userData->events_url,
            'received_events_url' => $userData->received_events_url,
            'type' => $userData->type,
            'email' => $userData->email ?: $email,
            'twitter_username' => $userData->twitter_username,
            'public_repos' => $userData->public_repos,
            'public_gists' => $userData->public_gists,
            'followers' => $userData->followers,
            'following' => $userData->following,
        ]);

        if (!$this->user) {
            $this->user = User::where("github_id", $githubData->github_id)->first();
            if (!$this->user && $this->user = User::whereIn("email", $emailList)->first() && !$this->authForExistingUser) {
                //User has discord account already?
                Respond::error("An email associated with your Github account already exists for a Discord account linked to this site. Please sign in with Discord and then connect your Github account from your Profile page.", [], "/");
            }
        }

        if ($this->user) {
            if ($this->user->banned)
                return $this->respond(false, "Your account has been banned from Submodica. Please contact an administrator for details if you believe this to be in error.");
            
            $this->user->github_id = $githubData->github_id;
            $this->user->save();

            $this->token->user_id = $this->user->id;
            $this->token->save();

            $this->user->setMeta("emails", implode(", ", $emailList));

            return $this->respond(true, "Welcome back to Submodica {$this->user->name}!", $this->user);
        } else {

            $this->user = new User([
                'name' => $githubData->login,
                'email' => $githubData->email ?: $email,
                'github_id' => $githubData->github_id,
                'password' => Hash::make($userData->id.strtotime('now').rand(10000, 100000).'@submodica'),
                'role_id' => 1
            ]);
            $this->user->save();

            $this->token->user_id = $this->user->id;
            $this->token->save();

            $this->user->setMeta("emails", implode(", ", $emailList));

            return $this->respond(true, "Welcome to Submodica {$this->user->name}!", $this->user);
        }
    }

    /**
     * @return array|false
     */
    public function updateDiscordInfo()
    {
        if ($this->token->exists && !$this->token->isValid())
            return false;

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token->access])->get('https://discord.com/api/users/@me');
        $userData = json_decode($response->body());

        if(!$this->user) {
            if (!$this->user && (UserMeta::where("key", "email")->where("value", "LIKE", "%{$userData->email}%")->first() || User::where("email", $userData->email)->where("discord_id", null)->first())) {
                //User has github account already?
                Respond::error("An email associated with your Discord account already exists for a Github account linked to this site. Please sign in with Github and then connect your Github account from your Profile page.", [], "/");
                die;
            }
            $this->user = User::where('discord_id', $userData->id)->first();
        }

        $discordData = [
            'discord_id' => $userData->id,
            'username' => $userData->username,
            'email' => $userData->email,
            'avatar' => $userData->avatar,
            'discriminator' => $userData->discriminator,
            'public_flags' => $userData->public_flags,
            'flags' => $userData->flags,
            'banner' => $userData->banner,
            'banner_color' => $userData->banner_color,
            'accent_color' => $userData->accent_color,
            'locale' => $userData->locale,
            'verified' => $userData->verified
        ];
        if ($existing = DiscordData::where('discord_id', $discordData['discord_id'])->first())
            $existing->update($discordData);
        else {
            $discordData = new DiscordData($discordData);
            $discordData->save();
        }

        if ($this->user) {
            if ($this->user->banned)
                return $this->respond(false, "Your account has been banned from Submodica. Please contact an administrator for details if you believe this to be in error.");

            $this->user->discord_id = $userData->id;
            $this->user->save();

            $this->token->user_id = $this->user->id;
            $this->token->save();

            return $this->respond(true, "Welcome back to Submodica {$userData->username}!", $this->user);
        } else {
            $this->user = new User([
                'name' => $userData->username,
                'email' => $userData->email,
                'discord_id' => $userData->id,
                'password' => Hash::make($userData->id.strtotime('now').'@submodica'),
                'role_id' => 1
            ]);
            $this->user->save();

            $this->token->user_id = $this->user->id;
            $this->token->save();

            return $this->respond(true, "Welcome to Submodica {$userData->username}!", $this->user);
        }
    }
}
