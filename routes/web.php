<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PendingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VersionController;
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

Route::middleware(['auth', 'verified', 'role:super-admin|admin'])->group(function () {
    // Dashboard
    Route::get('/redeem', [RedemptionController::class, 'index'])->name('redeem');

    Route::get('/getPendingCounts', [DashboardController::class, 'getPendingCounts'])->name('dashboard.getPendingCounts');

    Route::prefix('pending')->group(function () {
        Route::get('/', [PendingController::class, 'index'])->name('pending');
        Route::get('/getRedemptionCodeRequest', [PendingController::class, 'getRedemptionCodeRequest'])->name('pending.getRedemptionCodeRequest');

        Route::post('/handleRedemptionCodeRequest', [PendingController::class, 'handleRedemptionCodeRequest'])->name('pending.handleRedemptionCodeRequest');
    });

    /**
     * ==============================
     *           Members
     * ==============================
     */
    Route::prefix('member')->group(function () {
        // Listing Routes
        Route::get('/listing', [MemberController::class, 'listing'])->name('member.listing');
        Route::get('/getMemberListingData', [MemberController::class, 'getMemberListingData'])->name('member.getMemberListingData');

        Route::post('/addNewMember', [MemberController::class, 'addNewMember'])->name('member.addNewMember');
        Route::post('/updateMemberStatus', [MemberController::class, 'updateMemberStatus'])->name('member.updateMemberStatus');
        Route::post('/resetPassword', [MemberController::class, 'resetPassword'])->name('member.resetPassword');

        Route::delete('/deleteMember', [MemberController::class, 'deleteMember'])->name('member.deleteMember');
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

    /**
     * ==============================
     *        Version Control
     * ==============================
     */
    Route::prefix('version')->group(function () {
        Route::get('/', [VersionController::class, 'index'])->name('version');
        Route::get('/getVersionHistory', [VersionController::class, 'getVersionHistory'])->name('version.getVersionHistory');
        
        Route::post('/addVersion', [VersionController::class, 'addVersion'])->name('version.addVersion');
        Route::post('/updateVersion', [VersionController::class, 'updateVersion'])->name('version.updateVersion');
    });
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
