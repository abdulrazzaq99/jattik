<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Auth')->group(function () {
    Route::middleware('guest')->group(function(){
        //Manager Login
        Route::controller('LoginController')->group(function () {
            Route::get('/', 'showLoginForm')->name('login');
            Route::post('/', 'login');
            Route::get('logout', 'logout')->middleware('auth')->withoutMiddleware('guest')->name('logout');
        });

        //Manager Password Forgot
        Route::controller('ForgotPasswordController')->name('password.')->prefix('password')->group(function () {
            Route::get('reset', 'showLinkRequestForm')->name('request');
            Route::post('email', 'sendResetCodeEmail')->name('email');
            Route::get('code-verify', 'codeVerify')->name('code.verify');
            Route::post('verify-code', 'verifyCode')->name('verify.code');
        });

        //Manager Password Rest
        Route::controller('ResetPasswordController')->name('password.')->prefix('password')->group(function () {
            Route::get('password/reset/{token}', 'showResetForm')->name('reset.form');
            Route::post('password/reset/change', 'reset')->name('change');
        });
    });
});

Route::middleware('auth')->group(function () {
    Route::middleware(['check.status'])->group(function () {
        Route::middleware('manager')->group(function () {
            //Home Controller
            Route::controller('ManagerController')->group(function () {
                Route::get('dashboard', 'dashboard')->name('dashboard');

                //Manage Profile
                Route::get('password', 'password')->name('password');
                Route::get('profile', 'profile')->name('profile');
                Route::post('profile/update', 'profileUpdate')->name('profile.update.data');
                Route::post('password/update', 'passwordUpdate')->name('password.update.data');

                //Manage Branch
                Route::name('branch.')->prefix('branch')->group(function () {
                    Route::get('list', 'branchList')->name('index');
                    Route::get('income', 'branchIncome')->name('income');
                });
            });
            //Manage Staff
            Route::controller('StaffController')->name('staff.')->prefix('staff')->group(function () {
                Route::get('create', 'create')->name('create');
                Route::get('list', 'index')->name('index');
                Route::post('store', 'store')->name('store');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::post('status/{id}', 'status')->name('status');
            });

            //Manage Courier
            Route::controller('CourierController')->name('courier.')->prefix('courier')->group(function () {
                Route::get('list', 'courierInfo')->name('index');
                Route::get('dispatch/list', 'dispatchCourier')->name('dispatch');
                Route::get('upcoming/list', 'upcoming')->name('upcoming');
                Route::get('sent-queue/list', 'sentInQueue')->name('sentQueue');
                Route::get('delivery-queue/list', 'deliveryInQueue')->name('deliveryInQueue');
                Route::get('delivered', 'delivered')->name('delivered');
                Route::get('search', 'courierSearch')->name('search');
                Route::get('invoice/{id}', 'invoice')->name('invoice');
                Route::get('sent', 'sentCourier')->name('sent');
            });

            Route::controller('ManagerTicketController')->prefix('ticket')->name('ticket.')->group(function () {
                Route::get('/', 'supportTicket')->name('index');
                Route::get('/new', 'openSupportTicket')->name('open');
                Route::post('/create', 'storeSupportTicket')->name('store');
                Route::get('/view/{ticket}', 'viewTicket')->name('view');
                Route::post('/reply/{ticket}', 'replyTicket')->name('reply');
                Route::post('/close/{ticket}', 'closeTicket')->name('close');
                Route::get('/download/{ticket}', 'ticketDownload')->name('download');
            });
        });
    });
});
