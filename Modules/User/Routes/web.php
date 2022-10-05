<?php

use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {

    /** Dashboard */
    Route::get('/', 'HomeController@index');

    /*** Login Routes ***/
    Route::group(['middleware' => ['guest']], function () {

        Route::get('/login', 'Auth\LoginController@show');
        Route::post('/login', 'Auth\LoginController@login');

        Route::get('/recovery-options', 'Auth\ForgotPasswordController@showRecoveryOptionsForm');
        Route::get('/forget-password', 'Auth\ForgotPasswordController@showForgetPasswordForm');
        Route::post('/forget-password', 'Auth\ForgotPasswordController@submitForgetPasswordForm');
        Route::get('/reset-password/{token}', 'Auth\ForgotPasswordController@showResetPasswordForm');
        Route::post('/reset-password', 'Auth\ForgotPasswordController@submitResetPasswordForm');
    });

    Route::group(['middleware' => ['auth:web']], function () {

        /** Dashboard */
        Route::get('/dashboard', 'HomeController@index');
        Route::get('/logout', 'Auth\LogoutController@perform');

        /*** WhatCanDo Routes ***/
        Route::group(['prefix' => 'whatdo'], function () {
            Route::get('/', 'WhatDoController@index');
            Route::get('/create', 'WhatDoController@create');
            Route::post('/create', 'WhatDoController@store');
            Route::get('/show/{id}', 'WhatDoController@show');
            Route::get('/edit/{id}', 'WhatDoController@edit');
            Route::put('/update/{id}', 'WhatDoController@update');
            Route::delete('/delete/{id}', 'WhatDoController@destroy');
            Route::get('/search', 'WhatDoController@search');
        });

        /*** Appointments Routes ***/
        Route::group(['prefix' => 'appointments'], function () {
            Route::get('/', 'AppointmentController@index');
            Route::get('/create', 'AppointmentController@create');
            Route::post('/create', 'AppointmentController@store');
            Route::get('/show/{id}', 'AppointmentController@show');
            Route::get('/edit/{id}', 'AppointmentController@edit');
            Route::put('/update/{id}', 'AppointmentController@update');
            Route::delete('/delete/{id}', 'AppointmentController@destroy');
            Route::get('/search', 'AppointmentController@search');
        });

        /*** Customer Visits Routes ***/
        Route::group(['prefix' => 'sales'], function () {
            Route::get('/', 'SalesController@index');
            Route::get('/create', 'SalesController@create');
            Route::post('/create', 'SalesController@store');
            Route::get('/show/{id}', 'SalesController@show');
            Route::get('/edit/{id}', 'SalesController@edit');
            Route::put('/update/{id}', 'SalesController@update');
            Route::delete('/delete/{id}', 'SalesController@destroy');
            Route::get('/search', 'SalesController@search');
        });

        /*** Customer Visits Routes ***/
        Route::group(['prefix' => 'customer_visits'], function () {
            Route::get('/', 'CustomerVisitController@index');
            Route::get('/create', 'CustomerVisitController@create');
            Route::post('/create', 'CustomerVisitController@store');
            Route::get('/show/{id}', 'CustomerVisitController@show');
            Route::get('/edit/{id}', 'CustomerVisitController@edit');
            Route::put('/update/{id}', 'CustomerVisitController@update');
            Route::delete('/delete/{id}', 'CustomerVisitController@destroy');
            Route::get('/search', 'CustomerVisitController@search');
        });

        /** Products Routes */
        Route::group(['prefix' => 'products'], function () {
            Route::get('/', 'ProductsController@index');
            Route::get('/show/{id}', 'ProductsController@show');
            Route::get('/getItemProduct', 'ProductsController@getItemProduct');
            Route::get('/search', 'ProductsController@search');
            Route::get('/findPrice', 'ProductsController@findPrice');
        });

        /*** ACL Routes ***/
        Route::group(['prefix' => 'ACL'], function () {
            Route::group(['prefix' => 'roles'], function () {
                Route::get('/', 'ACL\RolesController@index');
                Route::get('/create', 'ACL\RolesController@create');
                Route::post('/create', 'ACL\RolesController@store');
                Route::get('/show/{id}', 'ACL\RolesController@show');
                Route::get('/edit/{id}', 'ACL\RolesController@edit');
                Route::put('/update/{id}', 'ACL\RolesController@update');
                Route::delete('/delete/{id}', 'ACL\RolesController@destroy');
                Route::get('/search', 'ACL\RolesController@search');
            });

            Route::group(['prefix' => 'permissions'], function () {
                Route::get('/', 'ACL\PermissionsController@index');
                Route::get('/create', 'ACL\PermissionsController@create');
                Route::post('/create', 'ACL\PermissionsController@store');
                Route::get('/{id}/show', 'ACL\PermissionsController@show');
                Route::get('/edit/{id}', 'ACL\PermissionsController@edit');
                Route::put('/update/{id}', 'ACL\PermissionsController@update');
                Route::delete('/{id}/delete', 'ACL\PermissionsController@destroy');
                Route::get('/search', 'ACL\PermissionsController@search');
            });
        });

        /*** User Routes ***/
        Route::group(['prefix' => 'users'], function () {
            Route::get('/', 'UserController@index');
            Route::get('/profile/{id}', 'UserController@showProfile');
            Route::get('/edit/profile/{id}', 'UserController@editProfile');
            Route::put('/update/profile/{id}', 'UserController@updateProfile');
        });

        /** Charts & Graphics Routes */
        Route::group(['prefix' => 'chart'], function () {
            Route::get('machines', 'ChartController@index');
        });

        /*** Customers Routes ***/
        Route::group(['prefix' => 'customers'], function () {
            Route::get('/', 'CustomersController@index');
            Route::get('/create', 'CustomersController@create');
            Route::post('/create', 'CustomersController@store');
            Route::get('/show/{id}', 'CustomersController@show');
            Route::get('/edit/{id}', 'CustomersController@edit');
            Route::put('/update/{id}', 'CustomersController@update');
            Route::delete('/delete/{id}', 'CustomersController@destroy');
            Route::get('/search', 'CustomersController@search');
        });

        /*** Reports Routes ***/
        Route::group(['prefix' => 'reports'], function () {
            Route::get('/customers', 'ReportsController@customers');
            Route::get('/customers/search', 'ReportsController@customers');
            Route::get('/products', 'ReportsController@products');
            Route::get('/schedules', 'ReportsController@schedules');
        });
    });
});
