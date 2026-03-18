<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
// ================================================LOGIN ROUTE=====================================================
Route::get('/', [LoginController::class, 'landingpage'])->name('landpage');
Route::get('/login', [LoginController::class, 'loginpage'])->name('login.page');
Route::post('/loginaccount', [LoginController::class, 'loginaccount'])->name('login.account');
Route::get('auth/google', [LoginController::class, 'redirect'])->name('google-auth');
Route::get('auth/google/callback', [LoginController::class, 'callbackGoogle']);
// Logout route
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
// Forgot Password Routes
Route::post('/forgot-password', [LoginController::class, 'sendNewPassword'])->name('forgot.password');
Route::get('/reset-password', [LoginController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [LoginController::class, 'updatePassword'])->name('password.update');
// ====================================================================================================================

// ==================================================REGISTER ROUTE====================================================
Route::get('/register', [LoginController::class, 'registerpage'])->name('login.register');
Route::post('/registeraccount', [LoginController::class, 'registeraccount'])->name('register.acc');

Route::get('/check-email', [LoginController::class, 'checkEmail']);
Route::get('/check-phone', [LoginController::class, 'checkPhone']);

// Email verification routes
Route::get('/email/verify', [LoginController::class, 'verificationNotice'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [LoginController::class, 'verifyEmail'])
    ->middleware(['signed'])
    ->name('verification.verify');

// AJAX route to check verification status
Route::get('/check-verification-status', [LoginController::class, 'checkVerificationStatus'])
    ->middleware('auth')
    ->name('verification.check');

// Resend Email verification routes
Route::post('/email/verification-notification', [LoginController::class, 'resendVerification'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');
// ====================================================================================================================
