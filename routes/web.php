<?php

use App\Http\Controllers\InvoicesPDFController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('site.index');
});

/** Print Invoice PDF External Routes */
Route::get('/customer_visits/{idReference}/generateInvoicePDF', [InvoicesPDFController::class, 'generateInvoicePDFCustomerVisit']);
Route::get('/sales/{idReference}/generateInvoicePDF', [InvoicesPDFController::class, 'generateInvoicePDFSale']);
