<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/search/{game}/{token}/{query?}', [ApiController::class, 'search'])
    ->where("game", "sn\d|sbz")
    ->where("token", "[\w\d]*")
    ->where('query', '.*')
    ->name("apiSearch");

Route::post('/getVersions', [ApiController::class, 'getVersions']);

Route::post('/recordGuid', [ApiController::class, 'recordGuid']);
Route::post('/getByGuid', [ApiController::class, 'getByGuid']);

