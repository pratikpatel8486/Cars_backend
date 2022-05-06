<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\LoginController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

$router->get('/clear-cache', function () {
    Artisan::call('config:cache');
    Artisan::call('config:clear');
});

Route::get('/test', function () {
    return "ok";
});
Route::post('admin/login', [LoginController::class, 'AdminLogin']);
// $router->post('admin/login', 'LoginController@AdminLogin');
