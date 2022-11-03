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

        /*** Multimedia Routes ***/
        Route::group(['prefix' => 'multimedia'], function () {
            Route::get('/', 'MultimediaController@index');
            Route::get('/create', 'MultimediaController@create');
            Route::post('/create', 'MultimediaController@store');
            Route::get('/show/{id}', 'MultimediaController@show');
            Route::get('/edit/{id}', 'MultimediaController@edit');
            Route::put('/update/{id}', 'MultimediaController@update');
            Route::delete('/delete/{id}', 'MultimediaController@destroy');
            Route::get('/search', 'MultimediaController@search');
            Route::get('/filter', 'MultimediaController@filter');

            Route::get('image-gallery/{id}', 'MultimediaController@imageGallery');
            Route::post('upload-file/', 'MultimediaController@uploadFile');
            Route::delete('delete-file/{id}', 'MultimediaController@destroyFile');
        });

        /*** Sales Routes ***/
        Route::group(['prefix' => 'sales'], function () {
            Route::get('/show/{id}', 'SalesController@show');
            Route::get('/search', 'SalesController@search');
            Route::get('/generateInvoicePDF', 'SalesController@generateInvoicePDF');
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

        /*** Customer Visits Routes ***/
        Route::group(['prefix' => 'customer_visits'], function () {
            Route::get('/show/{id}', 'CustomerVisitController@show');
            Route::get('/search', 'CustomerVisitController@search');
            Route::get('/generateInvoicePDF', 'CustomerVisitController@generateInvoicePDF');
        });

        /** Products Routes */
        Route::group(['prefix' => 'products'], function () {
            Route::get('/', 'ProductsController@index');
            Route::get('/create', 'ProductsController@create');
            Route::post('/create', 'ProductsController@store');
            Route::get('/show/{id}', 'ProductsController@show');
            Route::get('/edit/{id}', 'ProductsController@edit');
            Route::put('/update/{id}', 'ProductsController@update');
            Route::delete('/delete/{id}', 'ProductsController@destroy');
            Route::delete('/delete-product/{id}', 'ProductsController@destroy_product');
            Route::get('/search', 'ProductsController@search');

            Route::get('image-gallery/{id}', 'ProductsController@imageGallery');
            Route::post('upload-image/', 'ProductsController@uploadImage');
            Route::delete('image-delete/{id}', 'ProductsController@destroyImage');

            /** Import/Export CSV */
            Route::get('import-csv', 'ImportExportController@index');
            Route::post('import', 'ImportExportController@importcsv');
        });

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

        /*** Reports Routes ***/
        Route::group(['prefix' => 'reports'], function () {
            Route::get('/sellers', 'ReportsController@sellers');
            Route::get('/findSeller', 'ReportsController@findSeller');
            Route::get('/products', 'ReportsController@products');
            Route::get('/graphs', 'ReportsController@graphs');
            Route::get('/visit_on_map', 'ReportsController@visit_on_map');
            Route::get('/customer_visits', 'ReportsController@customer_visits');
            Route::get('/search_visits', 'ReportsController@search_visits');
            Route::get('/filter_visits', 'ReportsController@filter_visits');
            Route::get('/sales', 'ReportsController@sales');
        });
    });
});
