<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PageMediaController;
// ================================================LOGIN ROUTE=====================================================
Route::middleware('throttle:public')->group(function () {
    Route::get('/', [LoginController::class, 'landingpage'])->name('landpage');
    Route::get('/api/page-media', [PageMediaController::class, 'publicIndex'])->name('page-media.public');
    Route::view('/privacy-policy', 'legal.privacy-policy')->name('privacy.policy');
    Route::view('/terms-of-service', 'legal.terms-of-service')->name('terms.service');
    Route::view('/cookie-policy', 'legal.cookie-policy')->name('cookie.policy');
    Route::view('/contact-us', 'legal.contact-us')->name('contact.us');
    Route::view('/sitemap', 'legal.sitemap')->name('sitemap');
    Route::get('/login', [LoginController::class, 'loginpage'])->name('login.page');
    Route::get('/register', [LoginController::class, 'registerpage'])->name('login.register');
    Route::get('/reset-password', [LoginController::class, 'showResetForm'])->name('password.reset');
});
Route::post('/loginaccount', [LoginController::class, 'loginaccount'])
    ->middleware('throttle:auth')
    ->name('login.account');
Route::get('auth/google', [LoginController::class, 'redirect'])
    ->middleware('throttle:auth')
    ->name('google-auth');
Route::get('auth/google/callback', [LoginController::class, 'callbackGoogle'])
    ->middleware('throttle:auth');
// Logout route
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
// Forgot Password Routes
Route::post('/forgot-password', [LoginController::class, 'sendNewPassword'])
    ->middleware('throttle:auth')
    ->name('forgot.password');
Route::post('/reset-password', [LoginController::class, 'updatePassword'])
    ->middleware('throttle:auth')
    ->name('password.update');
// ====================================================================================================================

// ==================================================REGISTER ROUTE====================================================
Route::post('/registeraccount', [LoginController::class, 'registeraccount'])
    ->middleware('throttle:auth')
    ->name('register.acc');

Route::get('/check-email', [LoginController::class, 'checkEmail'])->middleware('throttle:auth');
Route::get('/check-phone', [LoginController::class, 'checkPhone'])->middleware('throttle:auth');

// Email verification routes
Route::get('/email/verify', [LoginController::class, 'verificationNotice'])
    ->middleware(['auth', 'throttle:public'])
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [LoginController::class, 'verifyEmail'])
    ->middleware(['signed', 'throttle:auth'])
    ->name('verification.verify');

// AJAX route to check verification status
Route::get('/check-verification-status', [LoginController::class, 'checkVerificationStatus'])
    ->middleware(['auth', 'throttle:api'])
    ->name('verification.check');

// Resend Email verification routes
Route::post('/email/verification-notification', [LoginController::class, 'resendVerification'])
    ->middleware(['auth', 'throttle:auth'])
    ->name('verification.send');
// ====================================================================================================================
