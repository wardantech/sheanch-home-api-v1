<?php



use App\Http\Controllers\Auth\OTPController;
use App\Http\Controllers\User\Auth\AuthController;
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

//User route
Route::group(['middleware' => ['auth:api']], function(){

    //landlord route
//    Route::group(['prefix' => 'user'], function(){
//        Route::apiResource('/', AdminController::class)->only(['index','store','show','update']);
//    });

    // Get Division Districts Thanas
    //Address route
    Route::group(['prefix' => 'settings'], function(){
        Route::get('divisions', [GetDivisionDistrictThanaController::class, 'getDivisions']);
        Route::post('districts', [GetDivisionDistrictThanaController::class, 'getDistricets']);
        Route::post('thanas', [GetDivisionDistrictThanaController::class, 'getThanas']);
    });

    // Property Route
    Route::group(['prefix' => 'property'], function() {
        Route::post('get-list', [PropertyController::class, 'getList']);
        Route::get('get-property-type', [PropertyController::class, 'getPropertyTypes']);
        Route::get('get-utilities', [PropertyController::class, 'getUtilities']);
        Route::get('get-facilities', [PropertyController::class, 'getFacilities']);
        Route::post('store', [PropertyController::class, 'store']);
        Route::post('image-upload/{id}',[PropertyController::class, 'imageUpload']);
    });
});

