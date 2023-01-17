<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\CustomersApiController;
use Modules\User\Http\Controllers\Api\AppointmentsApiController;
use Modules\User\Http\Controllers\Api\CustomerVisitApiController;
use Modules\User\Http\Controllers\Api\ProductsApiController;
use Modules\User\Http\Controllers\Api\HomeApiController;
use Modules\User\Http\Controllers\Api\MultimediaApiController;
use Modules\User\Http\Controllers\Api\OrderDetailApiController;
use Modules\User\Http\Controllers\Api\SalesApiController;
use Modules\User\Http\Controllers\Api\WhatDoApiController;

Route::group(['prefix' => 'auth'], function () {

    Route::group(['middleware' => ['guest']], function () {
        Route::post('login', 'Api\Auth\AuthController@login')->name('login');
        Route::post('register', 'Api\Auth\AuthController@register')->name('register');
    });

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('logout', 'Api\Auth\AuthController@logout');
        Route::get('user', 'Api\Auth\AuthController@user');
        Route::put('update', 'Api\Auth\AuthController@update');
    });
});

Route::middleware(['cors'])->group(function () {

    /*** Multimedia Routes ***/
    Route::group(['prefix' => 'multimedia'], function () {
        Route::get('/', [MultimediaApiController::class, 'index']);
        Route::get('/filter/{filter}', [MultimediaApiController::class, 'filter']);
    });

    /*** WhatDo Routes ***/
    Route::group(['prefix' => 'whatdo'], function () {
        Route::get('/getCustomerVisits/{idReference}', [WhatDoApiController::class, 'getCustomerVisits']);
        Route::get('/visit_on_map/{idReference}', [WhatDoApiController::class, 'visit_on_map']);
        Route::get('/filter/{filter}/{type}/{idReference}', [WhatDoApiController::class, 'filter']);
    });

    /*** Sales Routes ***/
    Route::group(['prefix' => 'sales'], function () {
        Route::get('/{idReference}', [SalesApiController::class, 'index']);
        Route::get('/getLastID/{idReference}', [SalesApiController::class, 'getLastID']); //Route use to create, get last id and autoincrement in app
        Route::post('/create', [SalesApiController::class, 'store']);
        Route::get('/edit/{id}', [SalesApiController::class, 'edit']);
        Route::put('/update/{id}', [SalesApiController::class, 'update']);
        Route::delete('/cancelSale/{id}/{idReference}', [SalesApiController::class, 'cancelSale']);
        Route::get('/search/{textSearch}/{idReference}', [SalesApiController::class, 'search']);
    });

    /*** Customer Visits Routes ***/
    Route::group(['prefix' => 'customer_visits'], function () {
        Route::get('/{idReference}', [CustomerVisitApiController::class, 'index']);
        Route::get('/getLastID/{idReference}', [CustomerVisitApiController::class, 'getLastID']); //Route use to create, get last id and autoincrement in app
        Route::post('/create', [CustomerVisitApiController::class, 'store']);
        Route::get('/edit/{id}', [CustomerVisitApiController::class, 'edit']);
        Route::put('/update/{id}', [CustomerVisitApiController::class, 'update']);
        Route::put('/update/pendingToProcess/{id}', [CustomerVisitApiController::class, 'pendingToProcess']);
        Route::delete('/cancelCustomerVisit/{id}/{idReference}', [CustomerVisitApiController::class, 'cancelCustomerVisit']);
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
        Route::get('/customer_visit/{visit_id}', [OrderDetailApiController::class, 'getOrderDetailsVisit']);
        Route::get('/sale/{sale_id}', [OrderDetailApiController::class, 'getOrderDetailsSale']);
        Route::post('/create', [OrderDetailApiController::class, 'store']);
        Route::delete('/customer_visit/deleteItemOrder/{visit_id}/{product_id}', [OrderDetailApiController::class, 'deleteItemOrderDetailVisit']);
        Route::delete('/sale/deleteItemOrder/{sale_id}/{product_id}', [OrderDetailApiController::class, 'deleteItemOrderDetailSale']);
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
