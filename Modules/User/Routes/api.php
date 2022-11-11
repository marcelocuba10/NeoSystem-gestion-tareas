<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\CustomersApiController;
use Modules\User\Http\Controllers\Api\AppointmentsApiController;
use Modules\User\Http\Controllers\Api\ProductsApiController;
use Modules\User\Http\Controllers\Api\HomeApiController;

Route::group(['prefix' => 'auth'], function () {

    Route::group(['middleware' => ['guest']], function () {
        Route::post('login', 'Api\Auth\AuthController@login')->name('login');
        Route::post('register', 'Api\Auth\AuthController@register')->name('register');
    });

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('logout', 'Api\Auth\AuthController@logout');
        Route::get('user', 'Api\Auth\AuthController@user');
    });
});

Route::middleware(['cors'])->group(function () {

    /** Dashboard */
    Route::get('/dashboard/{idReference}', [HomeApiController::class, 'index']);

    /*** Products Routes ***/
    Route::group(['prefix' => 'products'], function () {
        Route::get('/', [ProductsApiController::class, 'index']);
        Route::get('/search/{textSearch}', [ProductsApiController::class, 'search']);
    });

    /*** Appointments Routes ***/
    Route::group(['prefix' => 'appointments'], function () {
        Route::get('/{idReference}', [AppointmentsApiController::class, 'index']);
        Route::post('/create', [AppointmentsApiController::class, 'store']);
        Route::get('/search', [AppointmentsApiController::class, 'search']);
    });

    /*** Customers Routes ***/
    Route::group(['prefix' => 'customers'], function () {
        Route::get('/{idReference}', [CustomersApiController::class, 'index']);
        Route::post('/create', [CustomersApiController::class, 'store']);
        Route::get('/show/{id}', [CustomersApiController::class, 'show']);
        Route::put('/update/{id}', [CustomersApiController::class, 'update']);
        Route::delete('/delete/{id}', [CustomersApiController::class, 'destroy']);
        Route::get('/search/{textSearch}/{idReference}', [CustomersApiController::class, 'search']);
    });
});
