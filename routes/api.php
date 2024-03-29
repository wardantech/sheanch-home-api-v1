<?php

use App\Http\Controllers\Admin\Settings\DashboardController;
use App\Http\Controllers\Admin\Settings\DashboardSettingController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Accounts\BankController;
use App\Http\Controllers\Admin\Accounts\AccountController;

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
    return 5;
});
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::post('me', 'me');

});

Route::group(['middleware' => ['auth:api'], 'prefix' => 'auth'], function () {


    Route::group(['prefix' => 'dashboard'], function() {
        Route::post('store', [DashboardSettingController::class, 'store']);
        Route::post('update/{id}',[DashboardSettingController::class, 'update']);
    });

});



