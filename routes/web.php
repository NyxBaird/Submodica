<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

//Controllers
use \App\Http\Controllers\OAuthController;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\ModController;
use \App\Http\Controllers\ApiController;
use \App\Http\Controllers\MOTYController;
use \App\Http\Controllers\ModCommentsController;
use \App\Http\Controllers\Controller;
use \Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DocumentationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
| WHEN IN DOUBT CLEAR THE ROUTE CACHE WITH
| php artisan route:cache
*/

//Authenticated routes here
Route::group(['middleware' => 'auth'], function () {
    Route::get('/parseDocumentation', [DocumentationController::class, 'parse']);

    Route::post("/tags/search", [ApiController::class, "searchTags"]);

    Route::prefix("api")->group(function(){
        Route::get("/mods/fetchRepos", [ApiController::class, "fetchRepos"]);
        Route::get("/mods/fetchRepo/{repo}", [ApiController::class, "fetchRepo"]);
        Route::post("/mods/fetchRepoDirectories", [ApiController::class, "fetchRepoDirectories"]);

        Route::post("/verifyDiscordServerMembership", [ApiController::class, "verifyDiscordServerMembership"]);
    });

    Route::post("/mods/{game}/manage", [ModController::class, 'manage']);
    Route::post("/mods/{game}/manage/list", [ModController::class, 'manageList']);
    Route::post("/mods/{game}/favorites/list", [ModController::class, 'favoritesList']);

    Route::post("/mods/{game}/create", [ModController::class, 'create']);
    Route::post("/mods/{game}/{mod}/edit", [ModController::class, 'edit']);
    Route::post("/mods/{game}/{mod}/duplicate", [ModController::class, 'duplicate']);

    Route::post("/mods/{game}/{mod}/edit/saveDownloads", [ModController::class, 'saveDownloads']);
    Route::post("/mods/{game}/{mod}/edit/saveImage", [ModController::class, 'saveImage']);
    Route::post("/mods/{game}/{mod}/edit/saveAttributes", [ModController::class, 'saveAttributes']);
    Route::post("/mods/{game}/{mod}/edit/attributions/fetch", [ModController::class, 'fetchAttributions']);
    Route::post("/mods/{game}/{mod}/edit/preview", [ModController::class, 'updatePreviewStatus']);

    Route::get("/mods/{game}/{mod}/delete", [ModController::class, 'delete']);
    Route::post("/mods/{mod}/report", [ModController::class, 'report']);
    Route::post("/mods/{mod}/favorite", [ModController::class, 'favorite']);

    Route::post("/mods/{mod}/deleteImage/{image}", [ModController::class, 'deleteImage']);

    Route::post("/users/fetchManageable", [UserController::class, "fetchManageable"]);

    Route::get("/staff/ban/{user}", [UserController::class, "ban"]);
    Route::post("/staff/fetchAllImages", [ApiController::class, "fetchAllImages"]);
    Route::post("/staff/deleteImage/{image}", [ApiController::class, "deleteImage"]);
    Route::post('/staff/moty/getNominees', [MOTYController::class, "getNominees"]);
    Route::post('/staff/moty/saveNominee', [MOTYController::class, "saveNominee"]);
    Route::post('/staff/moty/deleteNominee/{nominee}', [MOTYController::class, "deleteNominee"]);

    //Profile page routes
    Route::post("/users/getConnectionInfo", [UserController::class, 'getConnectionInfo']);
    Route::post("/users/profile/update", [UserController::class, 'updateProfile']);


    //Mod Comment Routes
    Route::post("/mods/{mod}/comment", [ModCommentsController::class, 'create']);
    Route::post("/mods/{mod}/comment/{comment}", [ModCommentsController::class, 'create']);
    Route::post("/mods/{mod}/comment/{comment}/report", [ModCommentsController::class, 'report']);
    Route::delete("/mods/{mod}/comment/{comment}", [ModCommentsController::class, 'delete']);

    Route::post("/mods/{mod}/comment/{comment}/toggleLock", [ModCommentsController::class, 'toggleLock']);
    Route::post("/mods/{mod}/comment/{comment}/togglePin", [ModCommentsController::class, 'togglePin']);
});

//This group authenticates users
Route::prefix("auth")->group(function () {
    Route::get("verify/discord", [OAuthController::class, 'verifyDiscord']);
    Route::get("verify/github", [OAuthController::class, 'verifyGithub']);

    Route::post("disconnect", [OAuthController::class, 'disconnect']);

    //Return the oauth route for our requested provider
    Route::post("service", function (Request $request) {
        $url = false;
        if ($request->post("method") == "discord") {
            if (env("APP_ENV") == "local")
                $url = "https://discord.com/api/oauth2/authorize?client_id=" . env("DISCORD_ID") . "&redirect_uri=http%3A%2F%2Fmodnautica.local%2Fauth%2Fverify/discord&response_type=code&scope=identify%20email";
            else
                $url = "https://discord.com/api/oauth2/authorize?client_id=" . env("DISCORD_ID") . "&redirect_uri=https%3A%2F%2Fsubmodica.xyz%2Fauth%2Fverify/discord&response_type=code&scope=identify%20email";
        } else if ($request->post("method") == "github")
            $url = "https://github.com/login/oauth/authorize?client_id=" . env("GITHUB_ID") . "&scope=" . urlencode("user:email");// read:org

        if ($url) {
            if ($request->post("forExisting", false))
                Auth::user()->setMeta("AuthForExisting", true);

            die(json_encode(["url" => $url]));
        }
    });
});

Route::post("/track/download/{download}", [ModController::class, 'trackDownload']);
Route::post("/track/view/{mod}", [ModController::class, 'trackView']);

Route::post("/mods/{mod}/fetch", [ModController::class, 'fetch']);
Route::post("/mods/{mod}/fetchWithAll", [ModController::class, 'fetchWithAll']);
Route::post("/mods/{game}/search", [ModController::class, 'search']);
Route::post("/mods/{mod}/fetchRepo", [ModController::class, 'fetchRepo']);
Route::post("/mods/{mod}/fetchDownloads", [ModController::class, 'fetchDownloads']);
Route::post("/mods/{mod}/fetchImages", [ModController::class, 'fetchImages']);

Route::post("/mods/{mod}/fetchComments", [ModCommentsController::class, 'fetch']);
Route::post("/mods/{mod}/fetchComments/{comment}", [ModCommentsController::class, 'fetch']);

Route::post("/mods/getTrending", [ModController::class, 'getTrending']);
Route::post("/mods/{game}/list", [ModController::class, 'list']);

Route::post('/contact', [Controller::class, 'contact']);

Route::get("/logout", function (){
    Auth::logout();
    return redirect()->to("/");
});

Route::get('/link/external/{link}', function ($link) {
    dd($link);
});

//A catchall for our vue router
Route::get('/{any}', function () {
    if ($user = Auth::user())
        $user->load("role")->toArray();

    $mod = false;
    $uri = $_SERVER['REQUEST_URI'];
    preg_match("/\/mods\/(?:sn1|sbz)\/(?'mod_id'\d*)$/", $uri, $matches);
    if (isset($matches['mod_id']))
        $mod = \App\Models\Mod::query()->select(
            'mods.id',
            'mods.game',
            'mods.title',
            'mods.creator',
            'mods.tagline',
            DB::raw('(SELECT source FROM mod_images WHERE mod_id=mods.id and type="icon" LIMIT 1) AS profile_image'))->where('id', $matches['mod_id'])->first();

    return view('master', [
        "user" => $user,
        "mod" => $mod //This is used to set the meta tags for the mod page
    ]);
})->where("any", ".*")->name("app");
