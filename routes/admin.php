<?php

use App\Http\Controllers\Admin\Settings\FacilityController;
use App\Http\Controllers\Admin\Settings\GetDivisionDistrictThanaController;
use App\Http\Controllers\Admin\Settings\UtilityController;
use App\Http\Controllers\Admin\User\AdminController;
use App\Http\Controllers\Admin\User\LandlordController;
use App\Http\Controllers\Admin\User\TenantController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::get('as', function (){
    return 6;
});


Route::post('register',[AuthController::class,'register']);

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {

    Route::post('login', [AuthController::class,'login']);
    //Route::get('login', [AuthController::class,'login'])->name('login');
    Route::post('logout', [AuthController::class,'logout']);
    Route::post('refresh', [AuthController::class,'refresh']);
    Route::post('me', [AuthController::class,'me']);
});

//Admin route
Route::group(['middleware' => ['auth:api']], function(){

    //landlord route
    Route::group(['prefix' => 'user'], function(){
        Route::apiResource('/', AdminController::class)->only(['index','store','show','update']);
    });

    //landlord route
    Route::group(['prefix' => 'landlord'], function(){
        Route::post('store', [LandlordController::class,'store']);
        Route::post('list', [LandlordController::class,'list']);
        Route::get('show/{id}',[LandlordController::class,'show']);
        Route::post('update/{id}',[LandlordController::class,'update']);
        Route::post('image-upload/{id}',[LandlordController::class,'imageUpload']);

    });

    //Tenant route
    Route::group(['prefix' => 'tenant'], function(){
        Route::apiResource('/', TenantController::class)->only(['store','show','update']);
        Route::post('list', [TenantController::class,'list']);
        Route::get('show/{id}',[TenantController::class, 'show']);
        Route::post('update/{id}',[TenantController::class,'update']);
        Route::post('image-upload/{id}',[TenantController::class,'imageUpload']);
    });

    //Settings route
    Route::group(['prefix' => 'settings'], function(){

        //facility route
        Route::group(['prefix' => 'facility'], function(){
            Route::apiResource('/', FacilityController::class)->only(['store','show','update']);
            Route::post('list', [FacilityController::class,'list']);
        });

        //Utility route
        Route::group(['prefix' => 'utility'], function(){
            Route::apiResource('/', UtilityController::class)->only(['store','show','update']);
            Route::post('list', [UtilityController::class,'list']);
        });

        //Address route
        Route::group(['prefix' => ''], function(){
            Route::get('divisions', [GetDivisionDistrictThanaController::class, 'getDivisions']);
            Route::post('districts', [GetDivisionDistrictThanaController::class, 'getDistricets']);
            Route::post('thanas', [GetDivisionDistrictThanaController::class, 'getThanas']);
        });

    });

});
