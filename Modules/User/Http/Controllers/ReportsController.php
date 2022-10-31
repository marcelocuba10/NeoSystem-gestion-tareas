<?php

namespace Modules\User\Http\Controllers;

use PDF;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web', ['except' => ['logout']]);

        $this->middleware('permission:report-list|report-create|report-edit|report-delete', ['only' => ['index']]);
        $this->middleware('permission:report-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:report-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:report-delete', ['only' => ['destroy']]);
    }

    public function customers(Request $request)
    {
        $idRefCurrentUser = Auth::user()->idReference;
        $customers = DB::table('customers')
            ->where('idReference', '=', $idRefCurrentUser)
            ->select(
                'id',
                'name',
                'doc_id',
                'email',
                'city',
                'estate',
                'phone',
                'is_vigia',
                'next_visit_hour',
                'next_visit_date'
            )
            ->orderBy('created_at', 'DESC')
            ->paginate(30);

        if ($request->has('download')) {
            $customers = DB::table('customers')
                ->where('idReference', '=', $idRefCurrentUser)
                ->select(
                    'id',
                    'name',
                    'doc_id',
                    'email',
                    'city',
                    'estate',
                    'phone',
                    'is_vigia',
                    'next_visit_hour',
                    'next_visit_date'
                )
                ->orderBy('created_at', 'DESC')
                ->get();

            $pdf = PDF::loadView('user::reports.customersPrintPDF', compact('customers'));
            return $pdf->stream();
            // return $pdf->download('pdfview.pdf');
        }

        return view('user::reports.customers', compact('customers'))->with('i', (request()->input('page', 1) - 1) * 30);
    }

    public function products(Request $request)
    {
        $products = DB::table('products')
            ->select(
                'id',
                'name',
                'custom_code',
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
                    'custom_code',
                    'sale_price',
                    'updated_at',
                )
                ->orderBy('code', 'DESC')
                ->get();

            $pdf = PDF::loadView('user::reports.productsPrintPDF', compact('products'));
            return $pdf->stream();
            // return $pdf->download('pdfview.pdf');
        }

        return view('user::reports.products', compact('products'))->with('i', (request()->input('page', 1) - 1) * 30);
    }

    public function graphs()
    {
        $idRefCurrentUser = Auth::user()->idReference;
        $currentDate = Carbon::now()->format('d/m/Y');
        $currentOnlyYear = Carbon::now()->format('Y');

        /** For Pie Chart */
        $visits_cancel_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->where('customer_visits.status', '=', 'Cancelado')
            ->count();

        $visits_process_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->where('customer_visits.status', '=', 'Procesado')
            ->count();

        $visits_no_process_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->where('customer_visits.status', '=', 'No Procesado')
            ->count();

        $visits_pending_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->where('customer_visits.status', '=', 'Pendiente')
            ->count();

        $sales_count = DB::table('sales')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->where('sales.previous_type', '=', 'Venta')
            ->where('sales.status', '=', 'Procesado')
            ->count();

        $orders_count = DB::table('sales')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->where('sales.previous_type', '=', 'Presupuesto')
            ->where('sales.status', '!=', 'Cancelado')
            ->count();

        /** For Column Chart */
        $getSalesCountByMonth = DB::table('sales')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->selectRaw("count(sales.id) as total, date_format(sales.created_at, '%b %Y') as period")  //Essentially, what this selection date_format(created_at, '%b %Y') does is that it maps the created_at field into a string containing the field's month and year, like 'Mar 2022'.
            ->whereYear('sales.created_at', '>=', $currentOnlyYear)
            ->where('sales.type', '=', 'Venta')
            ->where('sales.status', '=', 'Procesado')
            ->orderBy('sales.created_at', 'ASC')
            ->groupBy('period')
            ->get();

        $getSalesCancelCountByMonth = DB::table('sales')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->selectRaw("count(sales.id) as total, date_format(sales.created_at, '%b %Y') as period")  //Essentially, what this selection date_format(created_at, '%b %Y') does is that it maps the created_at field into a string containing the field's month and year, like 'Mar 2022'.
            ->whereYear('sales.created_at', '>=', $currentOnlyYear)
            ->where('sales.type', '=', 'Venta')
            ->where('sales.status', '=', 'Cancelado')
            ->orderBy('sales.created_at', 'ASC')
            ->groupBy('period')
            ->get();

        $getOrdersCountByMonth = DB::table('sales')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->selectRaw("count(sales.id) as total, date_format(sales.created_at, '%b %Y') as period")  //Essentially, what this selection date_format(created_at, '%b %Y') does is that it maps the created_at field into a string containing the field's month and year, like 'Mar 2022'.
            ->whereYear('sales.created_at', '>=', $currentOnlyYear)
            ->where('sales.previous_type', '=', 'Presupuesto')
            ->where('sales.status', '!=', 'Cancelado')
            ->orderBy('sales.created_at', 'ASC')
            ->groupBy('period')
            ->get();

        $getOrdersCancelCountByMonth = DB::table('sales')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->selectRaw("count(sales.id) as total, date_format(sales.created_at, '%b %Y') as period")  //Essentially, what this selection date_format(created_at, '%b %Y') does is that it maps the created_at field into a string containing the field's month and year, like 'Mar 2022'.
            ->whereYear('sales.created_at', '>=', $currentOnlyYear)
            ->where('sales.previous_type', '=', 'Presupuesto')
            ->where('sales.status', '=', 'Cancelado')
            ->orderBy('sales.created_at', 'ASC')
            ->groupBy('period')
            ->get();

        $salesCountByMonth = $getSalesCountByMonth->pluck('total')->toArray();
        $salesCancelCountByMonth = $getSalesCancelCountByMonth->pluck('total')->toArray();
        $ordersCountByMonth = $getOrdersCountByMonth->pluck('total')->toArray();
        $ordersCancelCountByMonth = $getOrdersCancelCountByMonth->pluck('total')->toArray();

        $salesPeriods = $getSalesCountByMonth->pluck('period')->toArray();

        return view('user::reports.graphs', compact(
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
