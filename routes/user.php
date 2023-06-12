<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OTPController;
use App\Http\Controllers\User\WishlistController;
use App\Http\Controllers\User\Auth\AuthController;
use App\Http\Controllers\User\Review\ReviewController;
use App\Http\Controllers\User\Widgets\WidgetController;
use App\Http\Controllers\User\Profile\ProfileController;
use App\Http\Controllers\User\Accounts\ExpanseController;
use App\Http\Controllers\User\Accounts\RevenueController;
use App\Http\Controllers\User\Property\PropertyController;
use App\Http\Controllers\User\Property\PropertyAdController;
use App\Http\Controllers\User\Accounts\BankAccountController;
use App\Http\Controllers\User\Accounts\ExpanseItemController;
use App\Http\Controllers\User\Property\PropertyDeedController;
use App\Http\Controllers\User\Property\PropertyPageController;
use App\Http\Controllers\User\Dashboard\UserDasboardController;
use App\Http\Controllers\User\Property\RentCollectionController;
use App\Http\Controllers\User\Settings\GeneralSettingController;
use App\Http\Controllers\User\Property\DeedInformationController;
use App\Http\Controllers\User\Dashboard\TenantDashboardController;
use App\Http\Controllers\User\Accounts\MobileBankAccountController;
use App\Http\Controllers\User\Accounts\TransactionReportController;
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
Route::post('home', [GeneralSettingController::class, 'home'])->withoutMiddleware(['auth:api']);
Route::post('properties', [GeneralSettingController::class, 'properties'])->withoutMiddleware(['auth:api']);

// Dashboard controller
Route::post('get-dashboard-data', [UserDasboardController::class, 'getDashboardData']);
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
        Route::post('payment-reports', [PropertyController::class, 'paymentReports']);

        Route::group(['prefix' => 'ad'], function() {
            Route::post('store', [PropertyAdController::class, 'store']);
            Route::post('get-property-as-landlord', [PropertyAdController::class, 'getPropertyAsLandlord']);
            Route::post('list', [PropertyAdController::class,'getList']);
            Route::post('get-details', [PropertyAdController::class,'getDetails'])->withoutMiddleware(['auth:api']);
            Route::post('get-ad-details', [PropertyAdController::class,'getAdDetails'])->withoutMiddleware(['auth:api']);
            Route::post('get-edit-data', [PropertyAdController::class,'getEditData']);
            Route::post('active-property/list-as-type', [PropertyAdController::class,'getActivePropertyListAsType'])->withoutMiddleware(['auth:api']);
            Route::post('change-status/{id}',[PropertyAdController::class, 'changeStatus']);
            Route::post('update/{id}',[PropertyAdController::class, 'update']);
            Route::post('search',[PropertyAdController::class, 'search'])->withoutMiddleware(['auth:api']);
        });

        //
        Route::group(['prefix' => 'deed'], function(){
            Route::post('save-data', [PropertyDeedController::class, 'save'])->withoutMiddleware(['auth:api']);
            Route::post('request-list', [PropertyDeedController::class, 'requestDeed']);
            Route::post('apply-list', [PropertyDeedController::class, 'applyDeed']);
            Route::post('approved-list', [PropertyDeedController::class, 'approvedDeed']);
            Route::post('show', [PropertyDeedController::class, 'show']);
            Route::post('accept', [PropertyDeedController::class, 'accept']);
            Route::post('approve', [PropertyDeedController::class, 'approve']);
            Route::post('tenant-info', [PropertyDeedController::class, 'tenantInfo']);
            Route::post('decline', [PropertyDeedController::class, 'decline']);
            Route::post('delete/{id}',[PropertyDeedController::class, 'destroy']);

            Route::post('transaction-reports', [PropertyDeedController::class, 'transactionReports']);

            // Deed Information
            Route::post('information-data', [DeedInformationController::class, 'getData']);
            Route::post('information/store', [DeedInformationController::class, 'store']);
            Route::post('information/image/{id}', [DeedInformationController::class, 'imageUpload']);

            Route::post('get-rent-deed', [RentCollectionController::class, 'getRentDeed']);
            Route::post('get-property-info', [RentCollectionController::class, 'getPropertyInfo']);
            Route::post('get-accounts', [RentCollectionController::class, 'getAccounts']);
            Route::post('get-property-payments', [RentCollectionController::class, 'index']);
            Route::post('rent-property/store', [RentCollectionController::class, 'store']);
            Route::post('get-deed-transaction-month', [RentCollectionController::class, 'getDeedTransactionMonth']);
            Route::post('rent-property/edit', [RentCollectionController::class, 'edit']);
            Route::put('rent-property/update/{id}', [RentCollectionController::class, 'update']);
            Route::post('rent-property/due', [RentCollectionController::class, 'due']);
            Route::post('rent-property/due/store', [RentCollectionController::class, 'dueStore']);

            Route::post('delete-property-payment', [RentCollectionController::class, 'destroy']);
        });

    });

    Route::group(['prefix' => 'profile'], function(){
        // For landlord
        Route::post('/', [ProfileController::class, 'getUser']);
        Route::post('/sidebar', [ProfileController::class, 'sidebar']);
        Route::post('/show', [ProfileController::class, 'show']);
        Route::post('update/{id}', [ProfileController::class, 'update']);
        Route::post('image-upload/{id}', [ProfileController::class, 'imageUpload']);

        // Update password
        Route::post('password', [ProfileController::class, 'updatePassword']);
    });
});

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

    // Expanse Item Route
    Route::post('expanse-items', [ExpanseItemController::class, 'index']);
    Route::post('expanses-items/store', [ExpanseItemController::class, 'store']);
    Route::post('expanses-items/edit', [ExpanseItemController::class, 'edit']);
    Route::put('expanses-items/update/{id}', [ExpanseItemController::class, 'update']);
    Route::delete('expanses-items/{id}', [ExpanseItemController::class, 'destroy']);

    // Expanse Route
    Route::post('expanses', [ExpanseController::class, 'index']);
    Route::post('expanses/create', [ExpanseController::class, 'create']);
    Route::post('expanses/store', [ExpanseController::class, 'store']);
    Route::post('expanses/edit', [ExpanseController::class, 'edit']);
    Route::put('expanses/{id}', [ExpanseController::class, 'update']);
    Route::delete('expanses/{id}', [ExpanseController::class, 'destroy']);

    // Transaction Reports
    Route::post('cash', [TransactionReportController::class, 'cash']);
    Route::post('revenues', [TransactionReportController::class, 'revenues']);
    Route::post('property-transactions', [TransactionReportController::class, 'propertyTransactions']);
    Route::post('bank-transactions', [TransactionReportController::class, 'bankTransactions']);
    Route::post('mobile-bank-transactions', [TransactionReportController::class, 'mobileBankTransactions']);

    // Add bank account for user
    Route::post('banks', [BankAccountController::class, 'index']);
    Route::post('banks-store', [BankAccountController::class, 'store']);
    Route::post('banks-edit', [BankAccountController::class, 'edit']);
    Route::put('banks-update/{id}', [BankAccountController::class, 'update']);
    Route::delete('bank-method-delete/{id}', [BankAccountController::class, 'destroy']);
    Route::post('get-banks', [BankAccountController::class, 'getBanks']);

    // Add bank account for user
    Route::post('mobile-banks', [MobileBankAccountController::class, 'index']);
    Route::post('mobile-banks-store', [MobileBankAccountController::class, 'store']);
    Route::post('mobile-banks-edit', [MobileBankAccountController::class, 'edit']);
    Route::put('mobile-banks-update/{id}', [MobileBankAccountController::class, 'update']);
    Route::delete('mobile-method-delete/{id}', [MobileBankAccountController::class, 'destroy']);
    Route::post('get-mobile-banks', [MobileBankAccountController::class, 'getMobileBanks']);
});
