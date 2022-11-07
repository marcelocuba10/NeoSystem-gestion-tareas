<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\CustomersApiController;
use Modules\User\Http\Controllers\Api\AppointmentsApiController;

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

    /*** Appointments Routes ***/
    Route::group(['prefix' => 'appointments'], function () {
        Route::get('/{idReference}', [AppointmentsApiController::class, 'index']);
        Route::post('/create', [AppointmentsApiController::class, 'store']);
        Route::get('/show/{id}', [AppointmentsApiController::class, 'show']);
        Route::put('/update/{id}', [AppointmentsApiController::class, 'update']);
        Route::delete('/delete/{id}', [AppointmentsApiController::class, 'destroy']);
        Route::get('/search', [AppointmentsApiController::class, 'search']);
    });

    /*** Customers Routes ***/
    Route::group(['prefix' => 'customers'], function () {
        Route::get('/{idReference}', [CustomersApiController::class, 'index']);
        Route::post('/create', [CustomersApiController::class, 'store']);
        Route::get('/show/{id}', [CustomersApiController::class, 'show']);
        Route::put('/update/{id}', [CustomersApiController::class, 'update']);
        Route::delete('/delete/{id}', [CustomersApiController::class, 'destroy']);
        Route::get('/search', [CustomersApiController::class, 'search']);
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
