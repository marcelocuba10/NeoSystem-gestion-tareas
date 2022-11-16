<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\CustomersApiController;
use Modules\User\Http\Controllers\Api\AppointmentsApiController;
use Modules\User\Http\Controllers\Api\CustomerVisitApiController;
use Modules\User\Http\Controllers\Api\ProductsApiController;
use Modules\User\Http\Controllers\Api\HomeApiController;
use Modules\User\Http\Controllers\Api\OrderDetailApiController;

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

    /*** Customer Visits Routes ***/
    Route::group(['prefix' => 'customer_visits'], function () {
        Route::get('/{idReference}', [CustomerVisitApiController::class, 'index']);
        Route::get('/getLastID/{idReference}', [CustomerVisitApiController::class, 'getLastID']);
        Route::post('/create', [CustomerVisitApiController::class, 'store']);
        Route::get('/edit/{id}', [CustomerVisitApiController::class, 'edit']);
        Route::put('/update/{id}', [CustomerVisitApiController::class, 'update']);
        Route::delete('/delete/{id}', [CustomerVisitApiController::class, 'destroy']);
        Route::get('/search/{textSearch}/{idReference}', [CustomerVisitApiController::class, 'search']);
    });

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

    /*** Order Detail Routes ***/
    Route::group(['prefix' => 'order_detail'], function () {
        Route::get('/{visit_id}', [OrderDetailApiController::class, 'index']);
        Route::post('/create', [OrderDetailApiController::class, 'store']);
        Route::delete('/deleteItemOrder/{visit_id}/{product_id}', [OrderDetailApiController::class, 'destroyItemOrder']);
    });

    /*** Customers Routes ***/
    Route::group(['prefix' => 'customers'], function () {
        Route::get('/{idReference}', [CustomersApiController::class, 'index']);
        Route::post('/create', [CustomersApiController::class, 'store']);
        Route::put('/update/{id}', [CustomersApiController::class, 'update']);
        Route::delete('/delete/{id}', [CustomersApiController::class, 'destroy']);
        Route::get('/search/{textSearch}/{idReference}', [CustomersApiController::class, 'search']);
    });

    /** Dashboard */
    Route::get('/dashboard/{idReference}', [HomeApiController::class, 'index']);
});
