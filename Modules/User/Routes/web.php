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
        });

        /** Parameters Routes */
        Route::group(['prefix' => 'parameters'], function () {
            Route::get('/', 'ParametersController@index');
            Route::get('/create', 'ParametersController@create');
            Route::post('/create', 'ParametersController@store');
            Route::get('/show/{id}', 'ParametersController@show');
            Route::get('/edit/{id}', 'ParametersController@edit');
            Route::put('/update/{id}', 'ParametersController@update');
            Route::delete('/delete/{id}', 'ParametersController@destroy');
            Route::get('/search', 'ParametersController@search');
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
