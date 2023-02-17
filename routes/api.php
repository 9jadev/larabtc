<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminCustomerController;
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

    Route::prefix('customer')->group(function () {
        Route::post('/create', [CustomerController::class, 'create']);
        Route::post('/login', [CustomerController::class, 'login']);

        Route::get('/profile', [CustomerController::class, 'show'])->middleware(['auth:sanctum', 'type.customer']);

        Route::post('/list/dowliners', [CustomerController::class, 'refferaList'])->middleware(['auth:sanctum', 'type.customer']);

        Route::get('/logout', [CustomerController::class, 'logout'])->middleware(['auth:sanctum', 'type.customer']);

        Route::post('/updatekyc', [CustomerController::class, 'updateKyc'])->middleware(['auth:sanctum', 'type.customer']);

        Route::post('/updatekyc/image', [CustomerController::class, 'uploadKycImage'])->middleware(['auth:sanctum', 'type.customer']);

        Route::post('/updateprofile', [CustomerController::class, 'updateProfile'])->middleware(['auth:sanctum', 'type.customer']);
        Route::post('/updateprofile/image', [CustomerController::class, 'uploadImage'])->middleware(['auth:sanctum', 'type.customer']);

        Route::post('/login/otp', [CustomerController::class, 'otPlogin']);
        Route::post('/resetpassword', [CustomerController::class, 'resetPass']);

        Route::post('/changepassword', [CustomerController::class, 'resetPasswordCustomer'])->middleware(['auth:sanctum', 'type.customer']);

        Route::post('/transfer', [CustomerController::class, 'transfer'])->middleware(['auth:sanctum', 'type.customer']);

        Route::prefix('investment')->group(function () {
            Route::post('create', [InvestmentController::class, 'create'])->middleware(['auth:sanctum', 'type.customer']);
            Route::post('list', [InvestmentController::class, 'index'])->middleware(['auth:sanctum', 'type.customer']);
        });
        Route::prefix('deposits')->group(function () {
            Route::post('create', [DepositController::class, 'create'])->middleware(['auth:sanctum', 'type.customer']);
            Route::post('list', [DepositController::class, 'index'])->middleware(['auth:sanctum', 'type.customer']);

        });
    });

    Route::prefix('admin')->group(function () {
        Route::post('/create', [AdminController::class, 'store']);
        Route::post('/login', [AdminController::class, 'login']);
        Route::get('/showProfile', [AdminController::class, 'showProfile'])->middleware(['auth:sanctum', 'type.admin']);
        Route::get('/logout', [AdminController::class, 'logout'])->middleware(['auth:sanctum', 'type.admin']);

        Route::prefix('customer')->group(function () {
            Route::get('/', [AdminCustomerController::class, 'index'])->middleware(['auth:sanctum', 'type.admin']);
            Route::get('/{id}', [AdminCustomerController::class, 'show'])->middleware(['auth:sanctum', 'type.admin']);

            Route::get('/toggle/status', [AdminCustomerController::class, 'disabeCustomer'])->middleware(['auth:sanctum', 'type.admin']);

            Route::get('/toggle/invest', [AdminCustomerController::class, 'letInvest'])->middleware(['auth:sanctum', 'type.admin']);

            Route::post('/createbouns', [AdminCustomerController::class, 'createBouns'])->middleware(['auth:sanctum', 'type.admin']);

            Route::post('/createwithdraw', [AdminCustomerController::class, 'createWithdrawal'])->middleware(['auth:sanctum', 'type.admin']);

            Route::delete('/delete/{customer}', [CustomerController::class, 'destroy'])->middleware(['auth:sanctum', 'type.admin']);

            Route::get('/toggle/withdraw', [AdminCustomerController::class, 'letWithdraw'])->middleware(['auth:sanctum', 'type.admin']);

        });

        Route::prefix('deposit')->group(function () {
            Route::get('/', [DepositController::class, 'indexDeposit'])->middleware(['auth:sanctum', 'type.admin']);

            Route::get('/{deposit}', [DepositController::class, 'show'])->middleware(['auth:sanctum', 'type.admin']);

            Route::get('toggle/status/{deposit}', [DepositController::class, 'toggleStatus'])->middleware(['auth:sanctum', 'type.admin']);
        });

        Route::prefix('investment')->group(function () {
            Route::get('/', [InvestmentController::class, 'indexList'])->middleware(['auth:sanctum', 'type.admin']);
        });

        Route::prefix('paymentype')->group(function () {

            Route::post('/', [PaymentTypeController::class, 'store'])->middleware(['auth:sanctum', 'type.admin']);

            // Route::patch('/', [PaymentTypeController::class, 'update'])->middleware(['auth:sanctum', 'type.admin']);

            Route::patch('/{paymentType}', [PaymentTypeController::class, 'update'])->middleware(['auth:sanctum', 'type.admin']);

            Route::delete('/{paymentType}', [PaymentTypeController::class, 'destroy'])->middleware(['auth:sanctum', 'type.admin']);

        });

        Route::prefix('withdraw')->group(function () {
            Route::post('/', [WithdrawController::class, 'indexList'])->middleware(['auth:sanctum', 'type.admin']);

            Route::get('toggle/status/{withdraw}', [WithdrawController::class, 'toggleWithdraw'])->middleware(['auth:sanctum', 'type.admin']);
        });

    });

    Route::prefix('plans')->group(function () {
        Route::get('/list', [PlansController::class, 'index']);
        Route::post('/create', [PlansController::class, 'store']);
        Route::patch('/{plan}', [PlansController::class, 'update'])->middleware(['auth:sanctum', 'type.admin']);
        Route::delete('/{plan}', [PlansController::class, 'destroy'])->middleware(['auth:sanctum', 'type.admin']);

    });

    Route::prefix('payment')->group(function () {
        Route::get('/list', [PaymentController::class, 'index'])->middleware(['auth:sanctum', 'type.customer']);
        Route::get('/list/admin', [PaymentController::class, 'indexList'])->middleware(['auth:sanctum', 'type.admin']);

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
