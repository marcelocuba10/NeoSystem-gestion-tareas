<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class InvoicesPDFController extends Controller
{
    public function generateInvoicePDFCustomerVisit(Request $request, $idReference)
    {
        $customer_visit = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.id', '=', $request->visit_id)
            ->select(
                'customer_visits.id',
                'customer_visits.visit_number',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.next_visit_hour',
                'customer_visits.result_of_the_visit',
                'customer_visits.objective',
                'customer_visits.action',
                'customer_visits.type',
                'customer_visits.status',
                'customers.name AS customer_name',
                'customers.estate',
                'customers.doc_id',
                'customers.email',
                'customers.phone',
                'customers.address'
            )
            ->orderBy('customer_visits.created_at', 'DESC')
            ->first();

        $user = DB::table('users')
            ->where('users.idReference', '=', $idReference)
            ->select('name', 'phone_1', 'doc_id', 'address', 'email', 'city', 'estate')
            ->first();

        $order_details = DB::table('order_details')
            ->where('order_details.visit_id', '=', $request->visit_id)
            ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
            ->select(
                'products.name',
                'products.custom_code',
                'order_details.price',
                'order_details.quantity',
                'order_details.inventory',
                'order_details.amount'
            )
            ->orderBy('order_details.created_at', 'DESC')
            ->get();

        $total_order = DB::table('order_details')
            ->where('order_details.visit_id', '=', $request->visit_id)
            ->sum('amount');

        if ($request->has('download')) {
            $pdf = PDF::loadView('invoicesPDF.invoicePDFCustomerVisit', compact('user', 'customer_visit', 'order_details', 'total_order'));
            return $pdf->stream();
            // return $pdf->download('pdfview.pdf');
        }
    }

    public function generateInvoicePDFSale(Request $request, $idReference)
    {
        $sale = DB::table('sales')
            ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('sales.id', '=', $request->saleId)
            ->select(
                'customer_visits.id',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.next_visit_hour',
                'customer_visits.result_of_the_visit',
                'customer_visits.objective',
                'customer_visits.status',
                'customers.name AS customer_name',
                'customers.estate',
                'customers.doc_id',
                'customers.email',
                'customers.phone',
                'customers.address',
                'sales.invoice_number',
                'sales.visit_id',
                'sales.type',
                'sales.status',
                'sales.sale_date',
                'sales.order_date'
            )
            ->orderBy('sales.created_at', 'DESC')
            ->first();

        if (!$sale->visit_id) {
            $order_details = DB::table('order_details')
                ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
                ->where('order_details.sale_id', '=', $request->saleId)
                ->select(
                    'order_details.product_id',
                    'order_details.price',
                    'order_details.quantity',
                    'order_details.amount',
                    'products.custom_code',
                    'products.name',
                )
                ->orderBy('order_details.created_at', 'DESC')
                ->get();

            $total_order = DB::table('order_details')
                ->where('order_details.sale_id', '=', $request->saleId)
                ->sum('amount');
        } elseif ($sale->visit_id) {
            $order_details = DB::table('order_details')
                ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
                ->where('order_details.visit_id', '=', $sale->visit_id)
                ->select(
                    'order_details.product_id',
                    'order_details.price',
                    'order_details.quantity',
                    'order_details.amount',
                    'products.name',
                    'products.custom_code',
                )
                ->orderBy('order_details.created_at', 'DESC')
                ->get();

            $total_order = DB::table('order_details')
                ->where('order_details.visit_id', '=', $sale->visit_id)
                ->sum('amount');
        }

        $user = DB::table('users')
            ->where('users.idReference', '=', $idReference)
            ->select('name', 'phone_1', 'doc_id', 'address', 'email', 'city', 'estate')
            ->first();

        if ($request->has('download')) {
            $pdf = PDF::loadView('user::sales.invoicePDF.invoicePrintPDF', compact('user', 'sale', 'order_details', 'total_order'));
            return $pdf->stream();
            // return $pdf->download('pdfview.pdf');
        }
    }
}
