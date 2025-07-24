<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\PendingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RedemptionController;

Route::get('locale/{locale}', function ($locale) {
    App::setLocale($locale);
    Session::put("locale", $locale);

    return redirect()->back();
});

Route::get('/', function () {
    return redirect(route('login'));
});

Route::get('/redeem', [RedemptionController::class, 'index'])->middleware(['auth', 'verified'])->name('redeem');

Route::middleware('auth')->group(function () {
    Route::get('/getPendingCounts', [DashboardController::class, 'getPendingCounts'])->name('dashboard.getPendingCounts');

    Route::prefix('pending')->group(function () {
        Route::get('/', [PendingController::class, 'index'])->name('pending');
        Route::get('/getRedemptionCodeRequest', [PendingController::class, 'getRedemptionCodeRequest'])->name('pending.getRedemptionCodeRequest');

        Route::post('/handleRedemptionCodeRequest', [PendingController::class, 'handleRedemptionCodeRequest'])->name('pending.handleRedemptionCodeRequest');
    });

    /**
     * ==============================
     *            Redeem
     * ==============================
     */
    Route::prefix('redeem')->group(function () {
        Route::get('/getLicenses', [RedemptionController::class, 'getLicenses'])->name('redeem.getLicenses');

        Route::post('/redeemCode', [RedemptionController::class, 'redeemCode'])->name('redeem.redeemCode');
        /**
         * ==============================
         *        Redemption Codes
         * ==============================
         */
        Route::prefix('redemption_codes')->group(function () {
            Route::get('/', [RedemptionController::class, 'redemptionCodes'])->name('redeem.redemptionCodes');
            Route::get('/getRedemptionCodesData', [RedemptionController::class, 'getRedemptionCodesData'])->name('redeem.getRedemptionCodesData');
        });
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
