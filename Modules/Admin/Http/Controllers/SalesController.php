<?php

namespace Modules\Admin\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use PDF;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['logout']]);

        $this->middleware('permission:sales-sa-list|sales-sa-create|sales-sa-edit|sales-sa-delete', ['only' => ['index']]);
        $this->middleware('permission:sales-sa-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:sales-sa-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:sales-sa-delete', ['only' => ['destroy']]);
    }

    public function show($id)
    {
        $sale = DB::table('sales')
            ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('sales.id', '=', $id)
            ->select(
                'customer_visits.id',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.next_visit_hour',
                'customer_visits.result_of_the_visit',
                'customer_visits.objective',
                'customer_visits.status',
                'customers.name AS customer_name',
                'sales.invoice_number',
                'sales.visit_id',
                'sales.type',
                'sales.status',
                'sales.sale_date'
            )
            ->orderBy('sales.created_at', 'DESC')
            ->first();

        if (!$sale->visit_id) {
            $order_detail = DB::table('order_details')
                ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
                ->where('order_details.sale_id', '=', $id)
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
                ->where('order_details.sale_id', '=', $id)
                ->sum('amount');
        }

        if ($sale->visit_id) {
            $order_detail = DB::table('order_details')
                ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
                ->where('order_details.visit_id', '=', $sale->visit_id)
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
                ->where('order_details.visit_id', '=', $sale->visit_id)
                ->sum('amount');
        }

        return view('admin::sales.show', compact('sale', 'order_detail', 'total_order'));
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        if ($search == '') {
            $sales = DB::table('sales')
                ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                ->where('sales.isTemp', '!=', 1)
                ->select(
                    'sales.id',
                    'sales.invoice_number',
                    'sales.sale_date',
                    'sales.type',
                    'sales.status',
                    'sales.total',
                    'customers.name AS customer_name',
                    'customers.estate',
                    'customer_visits.visit_date',
                )
                ->orderBy('sales.created_at', 'DESC')
                ->paginate(20);
        } else {
            $sales = DB::table('sales')
                ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                ->where('customers.name', 'LIKE', "%{$search}%")
                ->orWhere('sales.invoice_number', 'LIKE', "%{$search}%")
                ->where('sales.isTemp', '!=', 1)
                ->select(
                    'sales.id',
                    'sales.invoice_number',
                    'sales.sale_date',
                    'sales.type',
                    'sales.status',
                    'sales.total',
                    'customers.name AS customer_name',
                    'customers.estate',
                    'customer_visits.visit_date',
                )
                ->orderBy('sales.created_at', 'DESC')
                ->paginate();
        }

        return view('admin::reports.sales', compact('sales', 'search'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function generateInvoicePDF(Request $request)
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
            ->select('name', 'phone_1', 'doc_id', 'address', 'email', 'city', 'estate')
            ->first();

        if ($request->has('download')) {
            $pdf = PDF::loadView('admin::sales.invoicePDF.invoicePrintPDF', compact('user', 'sale', 'order_details', 'total_order'));
            return $pdf->stream();
            // return $pdf->download('pdfview.pdf');
        }
    }
}
