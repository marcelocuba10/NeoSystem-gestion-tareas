<?php

use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {

    Route::get('/', 'HomeController@index');

    Route::group(['middleware' => ['guest']], function () {

        /*** Register Routes ***/
        // Route::get('/register', 'Auth\RegisterController@show');
        // Route::post('/register', 'Auth\RegisterController@register');

        /*** Login Routes ***/
        Route::get('/login', 'Auth\LoginController@show');
        Route::post('/login', 'Auth\LoginController@login');

        /*** forgot - reset password ***/
        Route::get('/recovery-options', 'Auth\ForgotPasswordController@showRecoveryOptionsForm');
        Route::get('/forget-password', 'Auth\ForgotPasswordController@showForgetPasswordForm');
        Route::post('/forget-password', 'Auth\ForgotPasswordController@submitForgetPasswordForm');
        Route::get('/reset-password/{token}', 'Auth\ForgotPasswordController@showResetPasswordForm');
        Route::post('/reset-password', 'Auth\ForgotPasswordController@submitResetPasswordForm');
    });

    Route::group(['middleware' => ['auth:web']], function () {

        Route::get('/dashboard', 'HomeController@index');
        Route::get('/logout', 'Auth\LogoutController@perform');

        /*** ACL Routes ***/
        Route::group(['prefix' => 'ACL'], function () {
            Route::group(['prefix' => 'roles'], function () {
                Route::get('/', 'ACL\RolesController@index');
                Route::get('/create', 'ACL\RolesController@create');
                Route::post('/create', 'ACL\RolesController@store');
                Route::get('/{id}/show', 'ACL\RolesController@show');
                Route::get('/edit/{id}', 'ACL\RolesController@edit');
                Route::put('/update/{id}', 'ACL\RolesController@update');
                Route::delete('/{id}/delete', 'ACL\RolesController@destroy');
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
            Route::get('/create', 'UserController@create');
            Route::post('/create', 'UserController@store');
            Route::get('/show/{id}', 'UserController@show');
            Route::get('/edit/{id}', 'UserController@edit');
            Route::put('/update/{id}', 'UserController@update');
            Route::delete('/delete/{id}', 'UserController@destroy');
            Route::get('/profile/{id}', 'UserController@showProfile');
            Route::get('/edit/profile/{id}', 'UserController@editProfile');
            Route::put('/update/profile/{id}', 'UserController@updateProfile');
            Route::get('/search', 'UserController@search');
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
            Route::get('/machines', 'ReportsController@machines');
            Route::get('/users', 'ReportsController@users');
            Route::get('/schedules', 'ReportsController@schedules');
        });
    });
});
