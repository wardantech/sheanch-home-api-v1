<?php



use App\Http\Controllers\Auth\OTPController;
use App\Http\Controllers\User\Auth\AuthController;
use App\Http\Controllers\User\Property\LeaseController;
use App\Http\Controllers\User\Property\PropertyAdController;
use App\Http\Controllers\User\Property\PropertyController;
use App\Http\Controllers\User\Settings\GetDivisionDistrictThanaController;
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


        Route::group(['prefix' => 'ad'], function() {
            Route::post('store', [PropertyAdController::class, 'store']);
            Route::post('get-property-as-landlord', [PropertyAdController::class, 'getPropertyAsLandlord']);
            Route::post('list', [PropertyAdController::class,'getList']);
            Route::post('active-property/list', [PropertyAdController::class,'getActivePropertyList'])->withoutMiddleware(['auth:api']);
            Route::post('change-status/{id}',[PropertyAdController::class, 'changeStatus']);
        });

    });

    //Lease route
    Route::group(['prefix' => 'lease'], function(){
        Route::post('store', [LeaseController::class, 'store']);
        Route::post('list', [LeaseController::class, 'getList']);

    });
});

