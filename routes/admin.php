<?php

use Illuminate\Support\Facades\Route;
use App\Models\Pages\AboutPropertySelling;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\User\AdminController;
use App\Http\Controllers\Admin\User\TenantController;
use App\Http\Controllers\Admin\Review\ReviewController;
use App\Http\Controllers\Admin\User\LandlordController;
use App\Http\Controllers\Admin\Accounts\ExpanseController;
use App\Http\Controllers\Admin\Settings\UtilityController;
use App\Http\Controllers\Admin\Pages\PropertyFaqController;
use App\Http\Controllers\Admin\Property\PropertyController;
use App\Http\Controllers\Admin\Settings\FacilityController;
use App\Http\Controllers\Admin\Widgets\HowItWorkController;
use App\Http\Controllers\Admin\Wishlists\WishlistController;
use App\Http\Controllers\Admin\Dashboard\DashboardController;
use App\Http\Controllers\Admin\Property\PropertyAdController;
use App\Http\Controllers\Admin\Accounts\ExpanseItemController;
use App\Http\Controllers\Admin\Accounts\TransactionController;
use App\Http\Controllers\Admin\Review\ReviewCommentController;
use App\Http\Controllers\Admin\Property\PropertyDeedController;
use App\Http\Controllers\Admin\Settings\PropertyTypeController;
use App\Http\Controllers\Admin\Accounts\Users\RevenueController;
use App\Http\Controllers\Admin\Settings\FrontendSettingController;
use App\Http\Controllers\Admin\Pages\AboutPropertySellingController;
use App\Http\Controllers\Admin\Pages\PropertyCustomerExperienceController;
use App\Http\Controllers\Admin\Settings\AreaController;
use App\Http\Controllers\Admin\Settings\GetDivisionDistrictThanaController;
use App\Http\Controllers\Admin\User\UserController;
use App\Models\Area;

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
Route::post('get-dashboard-data',[DashboardController::class, 'getDashbordData']);

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class,'login']);
    Route::post('logout', [AuthController::class,'logout']);
    Route::post('refresh', [AuthController::class,'refresh']);
    Route::post('me', [AuthController::class,'me']);
});

//Admin route
Route::group(['middleware' => ['auth:api']], function(){
    // users routes
    Route::group(['prefix' => 'users'], function() {
        Route::post('/', [UserController::class, 'index']);
        Route::post('/store', [UserController::class, 'store']);
        Route::post('/show', [UserController::class, 'show']);
        Route::post('/edit', [UserController::class, 'edit']);
        Route::put('/update/{user}', [UserController::class, 'update']);
        Route::post('image/{id}',[UserController::class, 'image']);
        Route::post('change-status/{id}',[UserController::class, 'changeStatus']);
        Route::delete('/delete/{id}', [UserController::class, 'destroy']);
    });

    // Account Routes
    Route::group(['prefix' => 'accounts'], function() {
        // Expance Item Routes
        Route::get('expanse-items', [ExpanseItemController::class, 'index']);
        Route::post('expanse-items', [ExpanseItemController::class, 'store']);
        Route::put('expanse-items/{expanseItem}', [ExpanseItemController::class, 'update']);
        Route::delete('expanse-items/{expanseItem}', [ExpanseItemController::class, 'destroy']);

        // Expanse Route
        Route::get('expanses', [ExpanseController::class, 'index']);
        Route::post('expanses', [ExpanseController::class, 'store']);
        Route::put('expanses/{expanse}', [ExpanseController::class, 'update']);
        Route::delete('expanses/{expanse}', [ExpanseController::class, 'destroy']);

        // Revenue Route
        // Route::get('revenues', [RevenueController::class, 'index']);
        // Route::post('revenues', [RevenueController::class, 'store']);
        // Route::put('revenues/{transaction}', [RevenueController::class, 'update']);
        // Route::delete('revenues/{transaction}', [RevenueController::class, 'destroy']);

        // Transactions Route
        Route::get('transactions', [TransactionController::class, 'index']);
    });

    // Property Route
    Route::group(['prefix' => 'property'], function() {
        Route::post('store', [PropertyController::class, 'store']);
        Route::get('show/{id}',[PropertyController::class, 'show']);
        Route::post('get-edit-data', [PropertyController::class, 'edit']);
        Route::post('get-create-data', [PropertyController::class, 'create']);
        Route::post('list', [PropertyController::class,'getList']);
        Route::post('change-status/{id}',[PropertyController::class, 'changeStatus']);
        Route::post('update/{id}',[PropertyController::class, 'update']);
        Route::post('delete/{id}',[PropertyController::class, 'destroy']);

        Route::group(['prefix' => 'ad'], function() {
            Route::post('store', [PropertyAdController::class, 'store']);
            Route::post('get-property-as-landlord', [PropertyAdController::class, 'getPropertyAsLandlord']);
            Route::post('list', [PropertyAdController::class,'getList']);
            Route::post('get-property-edit-data', [PropertyAdController::class,'getPropertyEditData']);
            Route::post('change-status/{id}',[PropertyAdController::class, 'changeStatus']);
            Route::post('update/{id}',[PropertyAdController::class, 'update']);
            Route::post('delete/{id}',[PropertyAdController::class, 'destroy']);
        });

        //Lease route
        Route::group(['prefix' => 'deed'], function(){
            Route::post('store', [PropertyDeedController::class, 'store']);
            Route::post('list', [PropertyDeedController::class, 'getList']);
            Route::post('change-status/{id}',[PropertyDeedController::class, 'changeStatus']);
            Route::post('delete/{id}',[PropertyDeedController::class, 'destroy']);
        });
    });

    //Review route
    Route::group(['prefix' => 'review'], function(){
        Route::post('store', [ReviewController::class,'store']);
        Route::post('list', [ReviewController::class,'getList']);
        Route::post('show',[ReviewController::class, 'show']);
        Route::post('update/{id}',[ReviewController::class,'update']);
        Route::post('change-status/{id}',[ReviewController::class, 'changeStatus']);
        Route::post('delete/{id}',[ReviewController::class, 'destroy']);

        Route::group(['prefix' => 'comment'], function(){
            Route::post('store', [ReviewCommentController::class,'store']);
            Route::post('list', [ReviewCommentController::class,'getList']);
            Route::post('show',[ReviewCommentController::class, 'show']);
            Route::post('update/{id}',[ReviewCommentController::class,'update']);
            Route::post('change-status/{id}',[ReviewCommentController::class, 'changeStatus']);
            Route::post('delete/{id}',[ReviewCommentController::class, 'destroy']);
        });
    });

    //Settings route
    Route::group(['prefix' => 'settings'], function(){

        //facility route
        Route::group(['prefix' => 'facility'], function(){
            Route::post('/', [FacilityController::class, 'store']);
            Route::post('list', [FacilityController::class,'getList']);
            Route::get('edit/{id}',[FacilityController::class, 'edit']);
            Route::post('update/{id}',[FacilityController::class, 'update']);
            Route::get('get-facilities',[FacilityController::class, 'getFacilities']);
            Route::post('change-status/{id}',[FacilityController::class, 'changeStatus']);
            Route::post('delete/{id}',[FacilityController::class, 'destroy']);
        });

        //Utility route
        Route::group(['prefix' => 'utility'], function(){
            Route::post('/', [UtilityController::class, 'store']);
            Route::post('list', [UtilityController::class,'getList']);
            Route::get('edit/{id}',[UtilityController::class, 'edit']);
            Route::post('update/{id}',[UtilityController::class, 'update']);
            Route::get('get-utilities',[UtilityController::class, 'getUtilities']);
            Route::post('change-status/{id}',[UtilityController::class, 'changeStatus']);
            Route::post('delete/{id}',[UtilityController::class, 'destroy']);
        });

        //Property type
        Route::group(['prefix' => 'property-type'], function(){
            Route::post('/', [PropertyTypeController::class, 'store']);
            Route::post('list', [PropertyTypeController::class,'getList']);
            Route::get('edit/{id}',[PropertyTypeController::class, 'edit']);
            Route::post('update/{id}',[PropertyTypeController::class, 'update']);
            Route::post('change-status/{id}',[PropertyTypeController::class, 'status']);
            Route::post('delete/{id}',[PropertyTypeController::class, 'destroy']);
        });

        Route::group(['prefix' => 'frontend'], function () {
            Route::post('general/store', [FrontendSettingController::class, 'store']);
            Route::post('get-data', [FrontendSettingController::class, 'getData']);
        });

        //Address route
        Route::group(['prefix' => ''], function(){
            Route::get('divisions', [GetDivisionDistrictThanaController::class, 'getDivisions']);
            Route::post('districts', [GetDivisionDistrictThanaController::class, 'getDistricets']);
            Route::post('thanas', [GetDivisionDistrictThanaController::class, 'getThanas']);
            Route::apiResource('area', AreaController::class)->only(['index','store','show','update','destroy']);
        });
    });

    // Pages route
    Route::group(['prefix' => 'pages'], function() {
        //Property
        Route::group(['prefix' => 'property'], function() {
            // Faq route
            Route::group(['prefix' => 'faq'], function() {
                Route::post('get-list', [PropertyFaqController::class, 'getLists']);
                Route::post('store', [PropertyFaqController::class, 'store']);
                Route::get('edit/{id}', [PropertyFaqController::class, 'edit']);
                Route::post('update/{id}', [PropertyFaqController::class, 'update']);
                Route::post('delete/{id}', [PropertyFaqController::class, 'destroy']);
                Route::post('change-status/{id}', [PropertyFaqController::class, 'changeStatus']);
            });

            // Customer Experiences
            Route::group(['prefix' => 'customer-experiences'], function() {
                Route::post('get-list', [PropertyCustomerExperienceController::class, 'getLists']);
                Route::post('store', [PropertyCustomerExperienceController::class, 'store']);
                Route::get('edit/{id}', [PropertyCustomerExperienceController::class, 'edit']);
                Route::post('update/{id}', [PropertyCustomerExperienceController::class, 'update']);
                Route::post('delete/{id}', [PropertyCustomerExperienceController::class, 'destroy']);
                Route::post('change-status/{id}', [PropertyCustomerExperienceController::class, 'changeStatus']);
            });

            // About property selling
            Route::group(['prefix' => 'about-selling'], function() {
                Route::post('get-list', [AboutPropertySellingController::class, 'getLists']);
                Route::post('store', [AboutPropertySellingController::class, 'store']);
                Route::get('edit/{id}', [AboutPropertySellingController::class, 'edit']);
                Route::post('update/{id}', [AboutPropertySellingController::class, 'update']);
                Route::post('delete/{id}', [AboutPropertySellingController::class, 'destroy']);
                Route::post('change-status/{id}', [AboutPropertySellingController::class, 'changeStatus']);
                Route::post('image-upload/{id}',[AboutPropertySellingController::class,'imageUpload']);
            });
        });
    });

    // Widgets routes
    Route::group(['prefix' => 'widgets'], function() {
        // How it work routes
        Route::group(['prefix' => 'how-it-works'], function() {
            Route::post('get-list', [HowItWorkController::class, 'getLists']);
            Route::post('store', [HowItWorkController::class, 'store']);
            Route::get('edit/{id}', [HowItWorkController::class, 'edit']);
            Route::post('update/{id}', [HowItWorkController::class, 'update']);
            Route::post('delete/{id}', [HowItWorkController::class, 'destroy']);
            Route::post('change-status/{id}', [HowItWorkController::class, 'changeStatus']);
        });
    });

    // Wishlists Routes
    Route::group(['prefix' => 'wishlist'], function() {
        Route::post('get-list', [WishlistController::class, 'getLists']);
        Route::post('delete/{id}',[WishlistController::class, 'destroy']);
    });

    // Review routes
    Route::group(['prefix' => 'reviews'], function() {
        Route::post('get-properties', [ReviewController::class, 'getPropertyReviews']);
        Route::post('get-landlords', [ReviewController::class, 'getLandlordsReviews']);
        Route::post('get-tenant', [ReviewController::class, 'getTenantReviews']);
        Route::post('delete', [ReviewController::class, 'destroy']);
    });
});
