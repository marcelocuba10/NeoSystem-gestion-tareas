<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {

    /** Dashboard */
    Route::get('/', 'HomeController@index');

    Route::group(['middleware' => ['guest']], function () {

        /** API Pool CronJob Route */
        Route::get('/machines/cron', 'MachinesController@cronjob');

        /*** Login Routes ***/
        Route::get('/login', 'Auth\LoginController@show');
        Route::post('/login', 'Auth\LoginController@login');

        /*** Reset Password ***/
        Route::get('recovery-options', 'Auth\ForgotPasswordController@showRecoveryOptionsForm');
        Route::get('forget-password', 'Auth\ForgotPasswordController@showForgetPasswordForm');
        Route::post('forget-password', 'Auth\ForgotPasswordController@submitForgetPasswordForm');
        Route::get('reset-password/{token}', 'Auth\ForgotPasswordController@showResetPasswordForm');
        Route::post('reset-password', 'Auth\ForgotPasswordController@submitResetPasswordForm');
    });

    Route::group(['middleware' => ['auth:admin']], function () {

        /** Dashboard */
        Route::get('/dashboard', 'HomeController@index');
        Route::get('/logout', 'Auth\LogoutController@perform');

        /*** User Routes ***/
        Route::group(['prefix' => 'users'], function () {
            Route::get('/', 'SuperUsersController@index');
            Route::get('/create', 'SuperUsersController@create');
            Route::post('/create', 'SuperUsersController@store');
            Route::get('/show/{id}', 'SuperUsersController@show');
            Route::get('/edit/{id}', 'SuperUsersController@edit');
            Route::put('/update/{id}', 'SuperUsersController@update');
            Route::delete('/delete/{id}', 'SuperUsersController@destroy');
            Route::get('/profile/{id}', 'SuperUsersController@showProfile');
            Route::get('/edit/profile/{id}', 'SuperUsersController@editProfile');
            Route::put('/update/profile/{id}', 'SuperUsersController@updateProfile');
            Route::get('/search', 'SuperUsersController@search');
        });

        /*** Sellers Routes ***/
        Route::group(['prefix' => 'sellers'], function () {
            Route::get('/', 'SellersController@index');
            Route::get('/create', 'SellersController@create');
            Route::post('/create', 'SellersController@store');
            Route::get('/show/{id}', 'SellersController@show');
            Route::get('/edit/{id}', 'SellersController@edit');
            Route::put('/update/{id}', 'SellersController@update');
            Route::delete('/delete/{id}', 'SellersController@destroy');
            Route::get('/search', 'SellersController@search');
        });

        /*** Financial Routes ***/
        Route::group(['prefix' => 'financial'], function () {
            Route::get('/', 'FinancialController@index');
            Route::get('/create', 'FinancialController@create');
            Route::post('/create', 'FinancialController@store');
            Route::get('/show/{id}', 'FinancialController@show');
            Route::get('/edit/{id}', 'FinancialController@edit');
            Route::put('/update/{id}', 'FinancialController@update');
            Route::delete('/delete/{id}', 'FinancialController@destroy');
            Route::get('/search', 'FinancialController@search');
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
                Route::any('/get', 'ACL\PermissionsController@getPermissions');
                Route::get('/create', 'ACL\PermissionsController@create');
                Route::post('/create', 'ACL\PermissionsController@store');
                Route::get('/show/{id}', 'ACL\PermissionsController@show');
                Route::get('/edit/{id}', 'ACL\PermissionsController@edit');
                Route::put('/update/{id}', 'ACL\PermissionsController@update');
                Route::delete('/delete/{id}', 'ACL\PermissionsController@destroy');
                Route::get('/search', 'ACL\PermissionsController@search');
            });
        });
    });
});
