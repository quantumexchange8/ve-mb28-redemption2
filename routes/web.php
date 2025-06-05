<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RedemptionController;

Route::get('/', function () {
    return redirect(route('login'));
});

Route::get('/dashboard', function () {
    return Inertia::render('Redeem');
})->middleware(['auth', 'verified'])->name('redeem');

Route::middleware('auth')->group(function () {
    Route::prefix('redeem')->group(function () {
        Route::get('/getLicenses', [RedemptionController::class, 'getLicenses'])->name('redeem.getLicenses');
        
        Route::post('/redeemCode', [RedemptionController::class, 'redeemCode'])->name('redeem.redeemCode');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
