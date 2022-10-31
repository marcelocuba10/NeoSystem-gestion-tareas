<?php

namespace Modules\Admin\Http\Controllers;

use PDF;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['logout']]);

        $this->middleware('permission:report-sa-list|report-sa-create|report-sa-edit|report-sa-delete', ['only' => ['index']]);
        $this->middleware('permission:report-sa-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:report-sa-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:report-sa-delete', ['only' => ['destroy']]);
    }

    public function sellers(Request $request)
    {
        $sellers = DB::table('users')
            ->select(
                'id',
                'idReference',
                'name',
                'last_name',
                'doc_id',
                'email',
                'phone_1',
                'seller_contact_1',
                'address',
                'city',
                'estate',
                'status',
            )
            ->orderBy('created_at', 'DESC')
            ->paginate(30);

        if ($request->has('download')) {
            $sellers = DB::table('users')
                ->select(
                    'id',
                    'idReference',
                    'name',
                    'last_name',
                    'doc_id',
                    'email',
                    'phone_1',
                    'seller_contact_1',
                    'address',
                    'city',
                    'estate',
                    'status',
                )
                ->orderBy('created_at', 'DESC')
                ->get();

            $pdf = PDF::loadView('admin::reports.sellersPrintPDF', compact('sellers'));
            return $pdf->stream();
            // return $pdf->download('pdfview.pdf');
        }

        return view('admin::reports.sellers', compact('sellers'))->with('i', (request()->input('page', 1) - 1) * 30);
    }

    public function products(Request $request)
    {
        $products = DB::table('products')
            ->select(
                'id',
                'name',
                'code',
                'custom_code',
                'purchase_price',
                'sale_price',
                'updated_at',
            )
            ->orderBy('created_at', 'DESC')
            ->paginate(30);

        if ($request->has('download')) {
            $products = DB::table('products')
                ->select(
                    'id',
                    'name',
                    'code',
                    'custom_code',
                    'purchase_price',
                    'sale_price',
                    'updated_at',
                )
                ->orderBy('code', 'DESC')
                ->get();

            $pdf = PDF::loadView('admin::reports.productsPrintPDF', compact('products'));
            return $pdf->stream();
            // return $pdf->download('pdfview.pdf');
        }

        return view('admin::reports.products', compact('products'))->with('i', (request()->input('page', 1) - 1) * 30);
    }

    public function graphs()
    {
        $idRefCurrentUser = Auth::user()->idReference;
        $currentDate = Carbon::now()->format('d/m/Y');
        $currentOnlyYear = Carbon::now()->format('Y');

        /** For Pie Chart */
        $visits_cancel_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.status', '=', 'Cancelado')
            ->count();

        $visits_process_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.status', '=', 'Procesado')
            ->count();

        $visits_no_process_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.status', '=', 'No Procesado')
            ->count();

        $visits_pending_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.status', '=', 'Pendiente')
            ->count();

        $sales_count = DB::table('sales')
            ->where('previous_type', '=', 'Venta')
            ->where('status', '=', 'Procesado')
            ->count();

        $orders_count = DB::table('sales')
            ->where('previous_type', '=', 'Presupuesto')
            ->where('status', '!=', 'Cancelado')
            ->count();

        /** For Column Chart */
        $getSalesCountByMonth = DB::table('sales')
            ->selectRaw("count(id) as total, date_format(created_at, '%b %Y') as period")  //Essentially, what this selection date_format(created_at, '%b %Y') does is that it maps the created_at field into a string containing the field's month and year, like 'Mar 2022'.
            ->whereYear('created_at', '>=', $currentOnlyYear)
            ->where('type', '=', 'Venta')
            ->where('status', '=', 'Procesado')
            ->orderBy('created_at', 'ASC')
            ->groupBy('period')
            ->get();

        $getSalesCancelCountByMonth = DB::table('sales')
            ->selectRaw("count(id) as total, date_format(created_at, '%b %Y') as period")  //Essentially, what this selection date_format(created_at, '%b %Y') does is that it maps the created_at field into a string containing the field's month and year, like 'Mar 2022'.
            ->whereYear('created_at', '>=', $currentOnlyYear)
            ->where('type', '=', 'Venta')
            ->where('status', '=', 'Cancelado')
            ->orderBy('created_at', 'ASC')
            ->groupBy('period')
            ->get();

        $getOrdersCountByMonth = DB::table('sales')
            ->selectRaw("count(id) as total, date_format(created_at, '%b %Y') as period")  //Essentially, what this selection date_format(created_at, '%b %Y') does is that it maps the created_at field into a string containing the field's month and year, like 'Mar 2022'.
            ->whereYear('created_at', '>=', $currentOnlyYear)
            ->where('previous_type', '=', 'Presupuesto')
            ->where('status', '!=', 'Cancelado')
            ->orderBy('created_at', 'ASC')
            ->groupBy('period')
            ->get();

        $getOrdersCancelCountByMonth = DB::table('sales')
            ->selectRaw("count(id) as total, date_format(created_at, '%b %Y') as period")  //Essentially, what this selection date_format(created_at, '%b %Y') does is that it maps the created_at field into a string containing the field's month and year, like 'Mar 2022'.
            ->whereYear('created_at', '>=', $currentOnlyYear)
            ->where('previous_type', '=', 'Presupuesto')
            ->where('status', '=', 'Cancelado')
            ->orderBy('created_at', 'ASC')
            ->groupBy('period')
            ->get();

        $salesCountByMonth = $getSalesCountByMonth->pluck('total')->toArray();
        $salesCancelCountByMonth = $getSalesCancelCountByMonth->pluck('total')->toArray();
        $ordersCountByMonth = $getOrdersCountByMonth->pluck('total')->toArray();
        $ordersCancelCountByMonth = $getOrdersCancelCountByMonth->pluck('total')->toArray();

        $salesPeriods = $getSalesCountByMonth->pluck('period')->toArray();

        return view('admin::reports.graphs', compact(
            'sales_count',
            'orders_count',
            'salesCountByMonth',
            'salesCancelCountByMonth',
            'ordersCountByMonth',
            'ordersCancelCountByMonth',
            'salesPeriods',
            'visits_cancel_count',
            'visits_pending_count',
            'visits_process_count',
            'visits_no_process_count',
        ));
    }
}
