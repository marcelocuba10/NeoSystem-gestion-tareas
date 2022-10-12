<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\MachineApiController;
use Modules\User\Http\Controllers\Api\NotificationApiController;
use Modules\User\Http\Controllers\Api\SchedulesApiController;
use Modules\User\Http\Controllers\Api\CustomersApiController;

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

    /*** Customers Routes ***/
    Route::group(['prefix' => 'customers'], function () {
        Route::get('/', [CustomersApiController::class, 'index']);
        Route::get('/create', 'CustomersApiController@create');
        Route::post('/create', 'CustomersApiController@store');
        Route::get('/show/{id}', 'CustomersApiController@show');
        Route::get('/edit/{id}', 'CustomersApiController@edit');
        Route::put('/update/{id}', 'CustomersApiController@update');
        Route::delete('/delete/{id}', 'CustomersApiController@destroy');
        Route::get('/search', 'CustomersApiController@search');
    });

    /** Routes Schedules */
    Route::get('schedules', [CustomersApiController::class, 'index']);
    Route::put('schedule/{id}', [SchedulesApiController::class, 'update']);
    Route::get('schedule/{id}', [SchedulesApiController::class, 'edit']);
    Route::get('schedule/user/{id}', [SchedulesApiController::class, 'getSchedulesByUser']);
    Route::post('schedule', [SchedulesApiController::class, 'store']);
    Route::get('schedule/user/check/{id}', [SchedulesApiController::class, 'checkSchedule']);
    Route::delete('schedule/{id}', [SchedulesApiController::class, 'destroy']);

    /** Routes Notifications */
    Route::get('notifications', [NotificationApiController::class, 'index']);

    /** Routes Machines */
    Route::get('machines', [MachineApiController::class, 'index']);
    Route::get('machine/{qrcode}', [MachineApiController::class, 'getMachineByQRcode']);
    Route::put('machine/{qrcode}', [MachineApiController::class, 'update']);
    Route::post('machine', [MachineApiController::class, 'store']);
});
