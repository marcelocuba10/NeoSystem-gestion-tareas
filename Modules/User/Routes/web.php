<?php

use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {

    /** Dashboard */
    Route::get('/', 'HomeController@index');

    /*** Login Routes ***/
    Route::group(['middleware' => ['guest']], function () {

        Route::get('/login', 'Auth\LoginController@show');
        Route::post('/login', 'Auth\LoginController@login');

        /*** Register Routes ***/
        Route::get('/register', 'Auth\RegisterController@show');
        Route::post('/register', 'Auth\RegisterController@register');
    });

    Route::group(['middleware' => ['auth:web']], function () {

        /** Send Email */
        Route::get('/send-email', 'SendEmailController@notifyEmail');

        /** Dashboard */
        Route::get('/dashboard', 'HomeController@index');
        Route::get('/logout', 'Auth\LogoutController@perform');

        /*** Tasks Routes ***/
        Route::group(['prefix' => 'tasks'], function () {
            Route::get('/', 'TasksController@index');
            Route::get('/create', 'TasksController@create');
            Route::post('/create', 'TasksController@store');
            Route::get('/show/{id}', 'TasksController@show');
            Route::get('/edit/{id}', 'TasksController@edit');
            Route::put('/update/{id}', 'TasksController@update');
            Route::delete('/delete/{id}', 'TasksController@destroy');
            Route::get('/search', 'TasksController@search');
            Route::get('/filter', 'TasksController@filter');
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
            Route::delete('/deleteItemOrder', 'CustomersController@destroyItemOrder');
            Route::get('/search', 'CustomersController@search');
        });

        /*** User Routes ***/
        Route::group(['prefix' => 'users'], function () {
            Route::get('/', 'UserController@index');
            Route::get('/show/{id}', 'UserController@show');
            Route::get('/profile/{id}', 'UserController@showProfile');
            Route::get('/edit/profile/{id}', 'UserController@editProfile');
            Route::put('/update/profile/{id}', 'UserController@updateProfile');
            Route::get('/search', 'UserController@search');
            Route::post('/profile/update-photo', 'UserController@updatePhotoProfile');
        });
    });
});
