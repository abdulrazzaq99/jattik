<?php


use Illuminate\Support\Facades\Route;

Route::namespace('Auth')->group(function () {
    Route::middleware('guest')->group(function () {
        //Staff Login
        Route::controller('LoginController')->group(function () {
            Route::get('/', 'showLoginForm')->name('login');
            Route::post('/', 'login');
            Route::get('logout', 'logout')->middleware('auth')->withoutMiddleware('guest')->name('logout');
        });
        //Staff Password Forgot
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
        Route::middleware('staff')->group(function () {
            //Home Controller
            Route::controller('StaffController')->group(function () {
                Route::get('dashboard', 'dashboard')->name('dashboard');
                Route::get('password', 'password')->name('password');
                Route::get('profile', 'profile')->name('profile');
                Route::post('profile/update', 'profileUpdate')->name('profile.update.data');
                Route::post('password/update', 'passwordUpdate')->name('password.update.data');
                Route::post('ticket/delete/{id}', 'ticketDelete')->name('ticket.delete');

                //Manage Branch
                Route::name('branch.')->prefix('branch')->group(function () {
                    Route::get('list', 'branchList')->name('index');
                    Route::get('income', 'branchIncome')->name('income');
                });
            });
            Route::controller('CourierController')->name('courier.')->prefix('courier')->group(function () {
                Route::get('send', 'create')->name('create');
                Route::post('store', 'store')->name('store');
                Route::post('update/{id}', 'update')->name('update');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::get('invoice/{id}', 'invoice')->name('invoice');
                Route::get('delivery/list', 'delivery')->name('delivery.list');
                Route::get('details/{id}', 'details')->name('details');
                Route::post('payment', 'payment')->name('payment');
                Route::post('delivery/store', 'deliveryStore')->name('delivery');
                Route::get('list', 'courierList')->name('manage.list');
                Route::get('date/search', 'courierDateSearch')->name('date.search');
                Route::get('search', 'courierSearch')->name('search');
                Route::get('send/list', 'sentCourierList')->name('manage.sent.list');
                Route::get('received/list', 'receivedCourierList')->name('received.list');
                //New Route
                Route::get('sent/queue', 'sentQueue')->name('sent.queue');
                Route::post('dispatch-all', 'courierAllDispatch')->name('dispatch.all');
                Route::get('dispatch', 'courierDispatch')->name('dispatch');
                Route::post('status/{id}', 'dispatched')->name('dispatched');
                Route::get('upcoming', 'upcoming')->name('upcoming');
                Route::post('receive/{id}', 'receive')->name('receive');
                Route::get('delivery/queue', 'deliveryQueue')->name('delivery.queue');
                Route::get('delivery/list/total', 'delivered')->name('manage.delivered');
            });

            Route::controller('CourierController')->prefix('cash')->group(function () {
                Route::get('collection', 'cash')->name('cash.courier.income');
            });

            Route::controller('CourierController')->group(function () {
                Route::get('customer/search', 'searchCustomer')->name('search.customer');
            });

            Route::controller('StaffTicketController')->prefix('ticket')->name('ticket.')->group(function () {
                Route::get('/', 'supportTicket')->name('index');
                Route::get('new', 'openSupportTicket')->name('open');
                Route::post('create', 'storeSupportTicket')->name('store');
                Route::get('view/{ticket}', 'viewTicket')->name('view');
                Route::post('reply/{ticket}', 'replyTicket')->name('reply');
                Route::post('close/{ticket}', 'closeTicket')->name('close');
                Route::get('download/{ticket}', 'ticketDownload')->name('download');
            });

            // Warehouse management routes (FR-17, FR-21)
            Route::controller('WarehouseController')->prefix('warehouse')->name('warehouse.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{holding}', 'show')->name('show');
                Route::post('/{holding}/add-package', 'addPackage')->name('add.package');
                Route::delete('/{holding}/package/{package}', 'removePackage')->name('remove.package');
                Route::post('/{holding}/consolidate', 'consolidate')->name('consolidate');
                Route::post('/{holding}/mark-shipped', 'markAsShipped')->name('mark.shipped');
                Route::get('/expiring/list', 'nearingExpiry')->name('expiring');
                // Shipping fee calculation (FR-21)
                Route::get('/{holding}/calculate-fee', 'showCalculateFee')->name('calculate.fee');
                Route::post('/{holding}/calculate-fee', 'calculateFee')->name('calculate.fee.post');
            });
        });
    });
});
