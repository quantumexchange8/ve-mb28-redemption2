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

Route::get('/redeem', function () {
    return Inertia::render('Redeem');
})->middleware(['auth', 'verified'])->name('redeem');

Route::middleware('auth')->group(function () {
    Route::get('/getPendingCounts', [DashboardController::class, 'getPendingCounts'])->name('dashboard.getPendingCounts');

    Route::prefix('pending')->group(function () {
        Route::get('/', [PendingController::class, 'index'])->name('pending');
        Route::get('/getRedemptionCodeRequest', [PendingController::class, 'getRedemptionCodeRequest'])->name('pending.getRedemptionCodeRequest');

        Route::post('/updateRedemptionRequest', [PendingController::class, 'updateRedemptionRequest'])->name('pending.updateRedemptionRequest');
        Route::post('/handleRedemptionCodeRequest', [PendingController::class, 'handleRedemptionCodeRequest'])->name('pending.handleRedemptionCodeRequest');
    });

    Route::prefix('redeem')->group(function () {
        Route::get('/getLicenses', [RedemptionController::class, 'getLicenses'])->name('redeem.getLicenses');
        
        Route::post('/redeemCode', [RedemptionController::class, 'redeemCode'])->name('redeem.redeemCode');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
