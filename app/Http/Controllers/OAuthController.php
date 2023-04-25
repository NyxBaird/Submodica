<?php
namespace App\Http\Controllers;

use App\Http\Services\OAuth;
use App\Http\Services\Respond;
use App\Models\DevLog;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use function PHPUnit\Framework\containsIdentical;

/**
 * Class OAuthController
 * @package App\Http\Controllers
 */
class OAuthController extends Controller
{
    /**
     * @var OAuth
     */
    private OAuth $oauth;

    /**
     * @var bool
     */
    private $rememberLogin = false;

    /**
     *
     */
    public function __construct() {
        $this->oauth = new OAuth(new Token());

        //WIP implement this better
        $this->rememberLogin = true;
    }

    public function disconnect(Request $request)
    {
        $service = $request->post("service");
        $user = Auth::user();

        if (
            ($service == "discord" && !$user->githubData) ||
            ($service == "github" && !$user->discordData)
        )
            Respond::error("You may not disconnect both connected accounts. (How would you log in?)");

        $user->{$service."Data"}->delete();
        $user->update([$service."_id" => null]);
        Respond::success("Your $service account was disconnected and you will not be able to log into Submodica through $service until you reconnect it!");
    }

    /**
     * @param Request $request
     * @return void
     */
    public function verifyGithub(Request $request)
    {
        $response = Http::asForm()->withHeaders([
            "Accept" => "application/json"
        ])->post('https://github.com/login/oauth/access_token', [
            'client_id' => env('GITHUB_ID'),
            'client_secret' => env('GITHUB_SECRET'),
            'code' => $request->input('code')
        ]);

        $data = json_decode($response->body());
        if (!$response->ok() || !isset($data->access_token))
            $this->returnError("Something went wrong while trying to connect to Github. Please refresh the page and try again.");

        //This should save our token and respond with [success, message, user]
        $userData = $this->oauth->saveTokenData("github", $data, session()->get("AuthForExisting", false));
        $this->processSavedTokenData($userData);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function verifyDiscord(Request $request)
    {
        $headers = [
            'grant_type' => 'authorization_code',
            'client_id' => env('DISCORD_ID'),
            'client_secret' => env('DISCORD_SECRET'),
            'redirect_uri' => AppUrl().'/auth/verify/discord',
            'scope' => 'identify email',
            'code' => $request->input('code')
        ];
        $response = Http::asForm()->post('https://discord.com/api/oauth2/token', $headers)->throw(function ($response, $e) use ($headers) {
            $log = new DevLog([
                'key' => 'discord_denial',
                'data' => [
                    'headers' => $headers,
                    'denial' => $e,
                ]
            ]);
            $log->save();
        });

        if (!$response->ok())
            $this->returnError();

        //New tokens should last for a week
        $tokenData = json_decode($response->body());

        //This should save our token and respond with [success, message, user]
        $userData = $this->oauth->saveTokenData("discord", $tokenData);
        $this->processSavedTokenData($userData);
    }

    /**
     * $data = [success, message, user]
     *
     * @param $data
     * @return void
     */
    private function processSavedTokenData($data)
    {
        if ($data["success"]) {
            $route = "/";
            if ($data["user"]) {
                Auth::login($data["user"], $this->rememberLogin);
                $route = "/users/profile";
            }

            Respond::success($data["message"], [], $route);
        } else
            Respond::error($data["message"], [], "/");
    }

    /**
     * @param $error
     * @return void
     */
    private function returnError($error = "") {
        Respond::error($error ?: "There was an error processing your request. Please reload the page and try again.", false, "/");
    }
}
