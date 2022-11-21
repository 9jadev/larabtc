<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\PlansController;
use App\Http\Controllers\WithdrawController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    // Route::post('/solotrack/store', [SoloTrackController::class, 'store']);
    // Route::get('/solotrack/all', [SoloTrackController::class, 'index']);
    //PaymentTypeController
    Route::post('/fund', [CustomerController::class, 'fundWallet']);

    Route::prefix('customer')->group(function () {
        Route::post('/create', [CustomerController::class, 'create']);
        Route::post('/login', [CustomerController::class, 'login']);
        Route::get('/profile', [CustomerController::class, 'show'])->middleware(['auth:sanctum', 'type.customer']);

        Route::get('/logout', [CustomerController::class, 'logout'])->middleware(['auth:sanctum', 'type.customer']);

        Route::post('/updatekyc', [CustomerController::class, 'updateKyc'])->middleware(['auth:sanctum', 'type.customer']);

        Route::post('/updatekyc/image', [CustomerController::class, 'uploadKycImage'])->middleware(['auth:sanctum', 'type.customer']);

        Route::post('/updateprofile', [CustomerController::class, 'updateProfile'])->middleware(['auth:sanctum', 'type.customer']);
        Route::post('/updateprofile/image', [CustomerController::class, 'uploadImage'])->middleware(['auth:sanctum', 'type.customer']);

        Route::post('/login/otp', [CustomerController::class, 'otPlogin']);
        Route::post('/resetpassword', [CustomerController::class, 'resetPass']);

        Route::prefix('investment')->group(function () {
            Route::post('create', [InvestmentController::class, 'create'])->middleware(['auth:sanctum', 'type.customer']);
            Route::post('list', [InvestmentController::class, 'index'])->middleware(['auth:sanctum', 'type.customer']);
        });
        Route::prefix('deposits')->group(function () {
            Route::post('create', [DepositController::class, 'create'])->middleware(['auth:sanctum', 'type.customer']);
            Route::post('list', [DepositController::class, 'index'])->middleware(['auth:sanctum', 'type.customer']);

        });
    });
    Route::prefix('plans')->group(function () {
        Route::get('/list', [PlansController::class, 'index']);

    });

    Route::prefix('payment')->group(function () {
        Route::get('/list', [PaymentController::class, 'index'])->middleware(['auth:sanctum', 'type.customer']);
        Route::post('/create', [PaymentController::class, 'create'])->middleware(['auth:sanctum', 'type.customer']);
    });

    Route::prefix('paymentype')->group(function () {
        Route::get('list', [PaymentTypeController::class, 'index']);
    });

    Route::prefix('withdraw')->group(function () {
        Route::post('list', [WithdrawController::class, 'index'])->middleware(['auth:sanctum', 'type.customer']);
        Route::post('create', [WithdrawController::class, 'create'])->middleware(['auth:sanctum', 'type.customer']);
    });

});
