<?php

use App\Http\Controllers\Admin\Accounts\AccountController;
use App\Http\Controllers\Admin\Accounts\BankController;
use App\Http\Controllers\Admin\Expense\ExpenseCategoryController;
use App\Http\Controllers\Admin\Expense\ExpenseController;
use App\Http\Controllers\Admin\Property\LeaseController;
use App\Http\Controllers\Admin\Property\PropertyAdManagerController;
use App\Http\Controllers\Admin\Property\PropertyController;
use App\Http\Controllers\Admin\Settings\FacilityCategoryController;
use App\Http\Controllers\Admin\Settings\FacilityController;
use App\Http\Controllers\Admin\Settings\GetDivisionDistrictThanaController;
use App\Http\Controllers\Admin\Settings\PropertyTypeController;
use App\Http\Controllers\Admin\Settings\UtilityCategoryController;
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


Route::post('register',[AuthController::class, 'register']);

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
        Route::post('list', [LandlordController::class,'getList']);
        Route::get('show/{id}',[LandlordController::class,'show']);
        Route::post('update/{id}',[LandlordController::class,'update']);
        Route::post('image-upload/{id}',[LandlordController::class,'imageUpload']);
        Route::get('get-landlords',[LandlordController::class,'getLandlords']);

        Route::post('change-status/{id}',[LandlordController::class, 'changeStatus']);
        Route::post('delete/{id}',[LandlordController::class, 'destroy']);
    });

    //Tenant route
    Route::group(['prefix' => 'tenant'], function(){
        Route::apiResource('/', TenantController::class)->only(['store','show','update']);
        Route::post('list', [TenantController::class,'getList']);
        Route::get('show/{id}',[TenantController::class, 'show']);
        Route::post('update/{id}',[TenantController::class,'update']);
        Route::post('image-upload/{id}',[TenantController::class,'imageUpload']);
        Route::post('change-status/{id}',[TenantController::class, 'changeStatus']);
        Route::post('delete/{id}',[TenantController::class, 'destroy']);
    });

    //Tenant route
    Route::group(['prefix' => 'expense'], function(){

        Route::group(['prefix' => 'category'], function() {
            Route::post('/', [ExpenseCategoryController::class, 'store']);
            Route::post('list', [ExpenseCategoryController::class, 'getList']);
            Route::get('show/{id}',[ExpenseCategoryController::class, 'show']);
            Route::post('update/{id}',[ExpenseCategoryController::class, 'update']);
            Route::post('change-status/{id}',[ExpenseCategoryController::class, 'changeStatus']);
            Route::post('delete/{id}',[ExpenseCategoryController::class, 'destroy']);
        });

        //expense
        Route::post('store', [ExpenseController::class,'store']);
        Route::post('list', [ExpenseController::class,'getList']);
        Route::get('get-categories', [ExpenseController::class, 'getCategories']);
        Route::get('show/{id}',[ExpenseController::class, 'show']);
        Route::post('update/{id}',[ExpenseController::class,'update']);
        Route::post('change-status/{id}',[ExpenseController::class, 'changeStatus']);
        Route::post('delete/{id}',[ExpenseController::class, 'destroy']);

    });


    Route::group(['prefix' => 'account'], function() {
        Route::post('store', [AccountController::class, 'store']);
        Route::post('list', [AccountController::class,'getList']);
        Route::get('show/{id}',[AccountController::class, 'show']);
        Route::post('update/{id}',[AccountController::class, 'update']);
        Route::post('change-status/{id}',[AccountController::class, 'changeStatus']);

        Route::group(['prefix' => 'bank'], function() {
            Route::post('store', [BankController::class, 'store']);
            Route::post('list', [BankController::class,'getList']);
            Route::get('show/{id}',[BankController::class, 'show']);
            Route::post('update/{id}',[BankController::class, 'update']);
            Route::post('change-status/{id}',[BankController::class, 'changeStatus']);
        });

    });


    // Property Route
    Route::group(['prefix' => 'property'], function() {
        Route::post('store', [PropertyController::class, 'store']);
        Route::get('show/{id}',[PropertyController::class, 'show']);
        Route::get('get-property-type', [PropertyController::class, 'getPropertyTypes']);
        Route::post('get-edit-data', [PropertyController::class, 'edit']);
        Route::post('list', [PropertyController::class,'getList']);
        Route::post('change-status/{id}',[PropertyController::class, 'changeStatus']);
        //Route::post('image-upload/{id}',[PropertyController::class,'imageUpload']);
        Route::post('update/{id}',[PropertyController::class, 'update']);
        Route::post('delete/{id}',[PropertyController::class, 'destroy']);

        Route::group(['prefix' => 'ad'], function() {
            Route::post('store', [PropertyAdManagerController::class, 'store']);
            Route::post('get-property-as-landlord', [PropertyAdManagerController::class, 'getPropertyAsLandlord']);
            Route::post('list', [PropertyAdManagerController::class,'getList']);
            Route::post('change-status/{id}',[PropertyAdManagerController::class, 'changeStatus']);
            Route::post('delete/{id}',[PropertyAdManagerController::class, 'destroy']);
        });

        //Lease route
        Route::group(['prefix' => 'lease'], function(){
            Route::post('store', [LeaseController::class, 'store']);
            Route::post('list', [LeaseController::class, 'getList']);
            Route::post('change-status/{id}',[LeaseController::class, 'changeStatus']);

        });

    });

    // Lease Route
    Route::group(['prefix' => 'lease'], function() {
        Route::post('store', [LeaseController::class, 'store']);
        Route::post('get-property-as-landlord', [LeaseController::class, 'getPropertyAsLandlord']);
        Route::post('list', [LeaseController::class,'getList']);
        Route::post('change-status/{id}',[LeaseController::class, 'changeStatus']);
        Route::post('image-upload/{id}',[LeaseController::class,'imageUpload']);
        Route::post('delete/{id}',[LeaseController::class, 'destroy']);
    });

    //Settings route
    Route::group(['prefix' => 'settings'], function(){

        //facility route
        Route::group(['prefix' => 'facility'], function(){
            // Facility category
            Route::group(['prefix' => 'category'], function() {
                Route::post('/', [FacilityCategoryController::class, 'store']);
                Route::post('list', [FacilityCategoryController::class, 'getList']);
                Route::get('show/{id}',[FacilityCategoryController::class, 'show']);
                Route::post('update/{id}',[FacilityCategoryController::class, 'update']);
                Route::post('change-status/{id}',[FacilityCategoryController::class, 'changeStatus']);
                Route::post('delete/{id}',[FacilityCategoryController::class, 'destroy']);
            });

            // Facility
            Route::post('/', [FacilityController::class, 'store']);
            Route::post('list', [FacilityController::class,'getList']);
            Route::get('get-categories', [FacilityController::class, 'getCategories']);
            Route::get('show/{id}',[FacilityController::class, 'show']);
            Route::post('update/{id}',[FacilityController::class, 'update']);
            Route::get('get-facilities',[FacilityController::class, 'getFacilities']);
            Route::post('change-status/{id}',[FacilityController::class, 'changeStatus']);
            Route::post('delete/{id}',[FacilityController::class, 'destroy']);
        });

        //Utility route
        Route::group(['prefix' => 'utility'], function(){
            // Utility Category
            Route::group(['prefix' => 'category'], function() {
                Route::post('/', [UtilityCategoryController::class, 'store']);
                Route::post('list', [UtilityCategoryController::class, 'getList']);
                Route::get('show/{id}',[UtilityCategoryController::class, 'show']);
                Route::post('update/{id}',[UtilityCategoryController::class, 'update']);
                Route::post('change-status/{id}',[UtilityCategoryController::class, 'changeStatus']);
                Route::post('delete/{id}',[UtilityCategoryController::class, 'destroy']);
            });

            // Utility
            Route::post('/', [UtilityController::class, 'store']);
            Route::post('list', [UtilityController::class,'getList']);
            Route::get('get-categories', [UtilityController::class, 'getCategories']);
            Route::get('show/{id}',[UtilityController::class, 'show']);
            Route::post('update/{id}',[UtilityController::class, 'update']);
            Route::get('get-utilities',[UtilityController::class, 'getUtilities']);
            Route::post('change-status/{id}',[UtilityController::class, 'changeStatus']);
            Route::post('delete/{id}',[UtilityController::class, 'destroy']);
        });

        //Property type
        Route::group(['prefix' => 'property-type'], function(){
            Route::post('/', [PropertyTypeController::class, 'store']);
            Route::post('list', [PropertyTypeController::class,'getList']);
            Route::get('show/{id}',[PropertyTypeController::class, 'show']);
            Route::post('update/{id}',[PropertyTypeController::class, 'update']);
            Route::post('change-status/{id}',[PropertyTypeController::class, 'status']);
            Route::post('delete/{id}',[PropertyTypeController::class, 'destroy']);
        });

        //Address route
        Route::group(['prefix' => ''], function(){
            Route::get('divisions', [GetDivisionDistrictThanaController::class, 'getDivisions']);
            Route::post('districts', [GetDivisionDistrictThanaController::class, 'getDistricets']);
            Route::post('thanas', [GetDivisionDistrictThanaController::class, 'getThanas']);
        });

    });


});
