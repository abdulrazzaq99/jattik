<?php

use App\Http\Controllers\Customer\Auth\LoginController;
use App\Http\Controllers\Customer\Auth\RegisterController;
use App\Http\Controllers\Customer\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
|
| Here are the routes for customer authentication and dashboard access.
| All routes use the 'customer' guard and are prefixed with '/customer'.
|
*/

// Guest customer routes (login, registration)
Route::middleware('customer.guest')->group(function () {

    // Registration routes
    Route::controller(RegisterController::class)->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register')->name('register.post');
        Route::get('register/verify', 'showVerifyForm')->name('register.verify');
        Route::post('register/verify', 'verifyOtp')->name('register.verify.post');
        Route::post('register/resend-otp', 'resendOtp')->name('register.resend');
    });

    // Login routes
    Route::controller(LoginController::class)->group(function () {
        Route::get('login', 'showLoginForm')->name('login');
        Route::post('login', 'login')->name('login.post');
        Route::get('login/verify', 'showVerifyForm')->name('login.verify');
        Route::post('login/verify', 'verifyOtp')->name('login.verify.post');
        Route::post('login/resend-otp', 'resendOtp')->name('login.resend');
    });

});

// Authenticated customer routes
Route::middleware('customer')->group(function () {

    // Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard routes
    Route::controller(DashboardController::class)->group(function () {
        Route::get('dashboard', 'index')->name('dashboard');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile/update', 'updateProfile')->name('profile.update');
        Route::get('password', 'password')->name('password');
        Route::post('password/update', 'updatePassword')->name('password.update');
        Route::get('track', 'trackCourier')->name('track');
        Route::get('sent-couriers', 'sentCouriers')->name('sent.couriers');
        Route::get('received-couriers', 'receivedCouriers')->name('received.couriers');
    });

});
