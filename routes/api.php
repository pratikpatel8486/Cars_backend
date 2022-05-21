<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\LoginController;
use App\Http\Controllers\admin\OtherController;
use App\Http\Controllers\admin\BrandModelController;
use App\Http\Controllers\admin\BrandVariantController;
use App\Http\Controllers\admin\CarController;
use App\Http\Controllers\admin\BrandController;
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
Route::post('admin/register', [LoginController::class, 'AdminRegister']);

Route::get('/get_all_brands', [BrandController::class, 'index']);
Route::get('/get_all_brand_modals/{id}', [BrandModelController::class, 'getModelsByBrand']);
Route::get('/get_all_brand_variants', [BrandVariantController::class, 'getVariantsByBrandModel']);
//Route::resource('/car', admin\CarController::class);
Route::get('/get_latest_cars', [CarController::class, 'getLatestCars']);


Route::group(['middleware' => ['jwt.verify']], function() {
    Route::post('/car', [CarController::class, 'store']);
    Route::put('/car/{id}', [CarController::class, 'update']);
    Route::delete('/car/{id}', [CarController::class, 'destroy']);
    Route::get('/car/{id}', [CarController::class, 'show']);

    Route::post('/brand', [BrandController::class, 'store']);
    Route::put('/brand/{id}', [BrandController::class, 'update']);
    Route::delete('/brand/{id}', [BrandController::class, 'destroy']);
    Route::get('/brand/{id}', [BrandController::class, 'show']);


    Route::post('/brand_model', [BrandModelController::class, 'store']);
    Route::put('/brand_model/{id}', [BrandModelController::class, 'update']);
    Route::delete('/brand_model/{id}', [BrandModelController::class, 'destroy']);
    Route::get('/brand_model/{id}', [BrandModelController::class, 'show']);

    Route::post('/brand_variant', [BrandVariantController::class, 'store']);
    Route::put('/brand_variant/{id}', [BrandVariantController::class, 'update']);
    Route::delete('/brand_variant/{id}', [BrandVariantController::class, 'destroy']);
    Route::get('/brand_variant/{id}', [BrandVariantController::class, 'show']);    
});