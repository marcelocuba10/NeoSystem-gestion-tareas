<?php

use App\Http\Controllers\InvoicesPDFController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('site.index');
});

Route::get('about', function () {
    return view('site.about');
});

Route::get('contact', function () {
    return view('site.contact');
});

Route::get('products', function () {
    return view('site.products');
});

/** Print Invoice PDF External Routes */
Route::get('/customer_visits/{idReference}/generateInvoicePDF', [InvoicesPDFController::class, 'generateInvoicePDFCustomerVisit']);
Route::get('/sales/{idReference}/generateInvoicePDF', [InvoicesPDFController::class, 'generateInvoicePDFSale']);
