<?php

use App\Http\Controllers\InvoicesPDFController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('site.index');
});

Route::get('/customer_visits/{idReference}/generateInvoicePDF', [InvoicesPDFController::class, 'generateInvoicePDFCustomerVisit']);

