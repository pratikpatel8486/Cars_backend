<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\LoginController;
use App\Http\Controllers\admin\OtherController;

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
Route::get('admin/get_all_brands', [OtherController::class, 'load_brands']);
Route::post('admin/get_all_brand_modals/{id}', [OtherController::class, 'load_modal_by_brand']);
Route::post('admin/add_car', [LoginController::class, 'add_car']);
// $router->post('admin/login', 'LoginController@AdminLogin');
