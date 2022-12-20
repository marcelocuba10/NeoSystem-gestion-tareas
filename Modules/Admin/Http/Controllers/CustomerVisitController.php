<?php

namespace Modules\Admin\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

use PDF;

class CustomerVisitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['logout']]);

        $this->middleware('permission:customer_visit-sa-list|customer_visit-sa-create|customer_visit-sa-edit|customer_visit-sa-delete', ['only' => ['index']]);
        $this->middleware('permission:customer_visit-sa-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:customer_visit-sa-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:customer_visit-sa-delete', ['only' => ['destroy']]);
    }

    public function show($id)
    {
        $customer_visit = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.id', '=', $id)
            ->where('customer_visits.isTemp', '!=', 1)
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
                'customers.estate'
            )
            ->orderBy('customer_visits.created_at', 'DESC')
            ->first();

        $order_details = DB::table('order_details')
            ->where('order_details.visit_id', '=', $id)
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
            ->where('order_details.visit_id', '=', $id)
            ->sum('amount');

        return view('admin::customer_visits.show', compact('customer_visit', 'order_details', 'total_order'));
    }

    public function generateInvoicePDF(Request $request)
    {
        $customer_visit = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.id', '=', $request->customer_visit)
            ->where('customer_visits.isTemp', '!=', 1)
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
            ->select('name', 'phone_1', 'doc_id', 'address', 'email', 'city', 'estate')
            ->first();

        $order_details = DB::table('order_details')
            ->where('order_details.visit_id', '=', $request->customer_visit)
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
            ->where('order_details.visit_id', '=', $request->customer_visit)
            ->sum('amount');

        if ($request->has('download')) {
            $pdf = PDF::loadView('admin::customer_visits.invoicePDF.invoicePrintPDF', compact('user', 'customer_visit', 'order_details', 'total_order'));
            return $pdf->stream();
            // return $pdf->download('pdfview.pdf');
        }
    }
}
