<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OTPController;
use App\Http\Controllers\User\Accounts\ExpanseController;
use App\Http\Controllers\User\WishlistController;
use App\Http\Controllers\User\Auth\AuthController;
use App\Http\Controllers\User\Review\ReviewController;
use App\Http\Controllers\User\Property\LeaseController;
use App\Http\Controllers\User\Widgets\WidgetController;
use App\Http\Controllers\User\Profile\ProfileController;
use App\Http\Controllers\User\Accounts\RevenueController;
use App\Http\Controllers\User\Auth\SocialLoginController;
use App\Http\Controllers\User\Property\PropertyController;
use App\Http\Controllers\User\Property\PropertyAdController;
use App\Http\Controllers\User\Property\PropertyDeedController;
use App\Http\Controllers\User\Property\PropertyPageController;
use App\Http\Controllers\User\Settings\GeneralSettingController;
use App\Http\Controllers\User\Dashboard\TenantDashboardController;
use App\Http\Controllers\User\Dashboard\LandlordDashboardController;
use App\Http\Controllers\User\Settings\GetDivisionDistrictThanaController;

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

//Frontend Data
Route::post('get-frontend-data', [GeneralSettingController::class, 'getFrontendData'])->withoutMiddleware(['auth:api']);
Route::get('get-frontend-banner-data', [GeneralSettingController::class, 'getFrontendBannerData'])->withoutMiddleware(['auth:api']);


// Dashboard controller
Route::post('get-landlord-dashboard-data', [LandlordDashboardController::class, 'getDashboardData']);
Route::post('get-tenant-dashboard-data', [TenantDashboardController::class, 'getDashboardData']);

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class,'login']);
    //Route::get('login', [AuthController::class,'login'])->name('login');
    Route::post('logout', [AuthController::class,'logout']);
    Route::post('refresh', [AuthController::class,'refresh']);
    Route::post('me', [AuthController::class,'me']);
});

Route::post('send-otp', [OTPController::class,'sendOTP']);
//Address route
Route::group(['prefix' => 'settings'], function(){
    Route::get('divisions', [GetDivisionDistrictThanaController::class, 'getDivisions']);
    Route::post('districts', [GetDivisionDistrictThanaController::class, 'getDistricets']);
    Route::post('thanas', [GetDivisionDistrictThanaController::class, 'getThanas']);
});
//User route
Route::group(['middleware' => ['auth:api']], function(){

    // Property Route
    Route::group(['prefix' => 'property'], function() {
        Route::post('store', [PropertyController::class, 'store']);
        Route::post('list', [PropertyController::class, 'getList']);
        Route::get('show/{id}', [PropertyController::class, 'show'])->withoutMiddleware(['auth:api']);
        Route::get('get-property-type', [PropertyController::class, 'getPropertyTypes'])->withoutMiddleware(['auth:api']);
        Route::post('get-create-data', [PropertyController::class, 'create']);
        Route::post('edit', [PropertyController::class, 'edit']);
        Route::post('update/{id}', [PropertyController::class, 'update']);
        Route::get('details/{id}', [PropertyController::class, 'details']);
        Route::get('landlord/details/{id}', [PropertyController::class, 'landlordDetails']);

        Route::group(['prefix' => 'ad'], function() {
            Route::post('store', [PropertyAdController::class, 'store']);
            Route::post('get-property-as-landlord', [PropertyAdController::class, 'getPropertyAsLandlord']);
            Route::post('list', [PropertyAdController::class,'getList']);
            Route::post('get-details', [PropertyAdController::class,'getDetails'])->withoutMiddleware(['auth:api']);
            Route::post('get-edit-data', [PropertyAdController::class,'getEditData']);
            Route::post('active-property/list', [PropertyAdController::class,'getActivePropertyList'])->withoutMiddleware(['auth:api']);
            Route::post('active-property/list-as-type', [PropertyAdController::class,'getActivePropertyListAsType'])->withoutMiddleware(['auth:api']);
            Route::post('change-status/{id}',[PropertyAdController::class, 'changeStatus']);
            Route::post('update/{id}',[PropertyAdController::class, 'update']);
            Route::post('search',[PropertyAdController::class, 'search'])->withoutMiddleware(['auth:api']);
        });

        //
        Route::group(['prefix' => 'deed'], function(){
            Route::post('save-data', [PropertyDeedController::class, 'save'])->withoutMiddleware(['auth:api']);
            Route::post('tenant-list', [PropertyDeedController::class, 'getListTenant']);
            Route::post('landlord-list', [PropertyDeedController::class, 'getListLandlord']);
            Route::post('change-status/{id}',[PropertyDeedController::class, 'changeStatus']);
            Route::post('delete/{id}',[PropertyDeedController::class, 'destroy']);
        });

    });


    Route::group(['prefix' => 'profile'], function(){
        // For landlord
        Route::post('landlord', [ProfileController::class, 'getLandlordData']);
        Route::post('landlord/show', [ProfileController::class, 'showLandlord']);
        Route::post('landlord/update/{id}', [ProfileController::class, 'landlordUpdate']);
        Route::post('landlord/image-upload/{id}', [ProfileController::class, 'imageUpload']);

        // For Tenant
        Route::post('tenant', [ProfileController::class, 'getTenantData']);
        Route::post('tenant/update/{id}', [ProfileController::class, 'TenantUpdate']);
        Route::post('tenant/show', [ProfileController::class, 'showTenant']);
        Route::post('tenant/tenant-image-upload/{id}', [ProfileController::class, 'tenantImageUpload']);

        // Update password
        Route::post('landlord/password', [ProfileController::class, 'updatePassword']);
    });
});


// Get widgets route
Route::get('get-how-to-work-widget', [WidgetController::class, 'getHowToWork'])->withoutMiddleware(['auth:api']);

// Get Property Page Data
Route::get('get-property-faq-data', [PropertyPageController::class, 'getFaq']);
Route::get('get-property-page-data', [PropertyPageController::class, 'getCustomerExperiences']);
Route::get('get-about-property-selling-data', [PropertyPageController::class, 'getAboutPropertySelling']);

// Wishlist route
Route::group(['prefix' => 'wishlist'], function() {
    Route::post('get-lists', [WishlistController::class, 'getLists']);
    Route::post('store', [WishlistController::class, 'store']);
    Route::post('delete', [WishlistController::class, 'destroy']);
});

// Review Routes
Route::group(['prefix' => 'review'], function() {
    Route::post('store', [ReviewController::class, 'store']);
    Route::post('get-reviews', [ReviewController::class, 'getReviews']);
});

// Accounts
Route::group(['prefix' => 'accounts', 'namespace' => 'User'], function() {
    // Revenues Route
    Route::get('revenues', [RevenueController::class, 'index']);
    Route::post('revenues', [RevenueController::class, 'store']);
    Route::put('revenues/{transaction}', [RevenueController::class, 'update']);
    Route::delete('revenues/{transaction}', [RevenueController::class, 'destroy']);

    // Expanse Route
    Route::get('expanses', [ExpanseController::class, 'index']);
    Route::post('expanses', [ExpanseController::class, 'store']);
    Route::put('expanses/{transaction}', [ExpanseController::class, 'update']);
    Route::delete('expanses/{transaction}', [ExpanseController::class, 'destroy']);
});
