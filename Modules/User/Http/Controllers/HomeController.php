<?php

namespace Modules\User\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web', ['except' => ['logout']]);
    }

    public function index()
    {
        $idRefCurrentUser = Auth::user()->idReference;
        $currentDate = Carbon::now()->format('d/m/Y');
        $currentOnlyYear = Carbon::now()->format('Y');
        $currentMonth = Carbon::now()->format('m');

        Carbon::setlocale('ES');
        $currentMonthName = Carbon::parse(Carbon::now()->format('Y/m/d'))->translatedFormat('F');

        $customer_visits = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
            ->select(
                'customer_visits.id',
                'customer_visits.visit_number',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.next_visit_hour',
                'customer_visits.status',
                'customer_visits.action',
                'customer_visits.type',
                'customers.name AS customer_name',
                'customers.estate',
                'customers.phone',
            )
            ->orderBy('customer_visits.created_at', 'DESC')
            ->limit(4)
            ->get();

        $sales = DB::table('sales')
            ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('sales.seller_id', '=', $idRefCurrentUser)
            ->orWhere('customer_visits.seller_id', '=', $idRefCurrentUser)
            ->select(
                'sales.id',
                'sales.customer_id',
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
            ->limit(5)
            ->get();

        $cant_customers = DB::table('customers')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->count();

        $visited_less_30_days = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->where('visit_date', '>', Carbon::now()->subDays(30))
            ->count();

        $visited_more_30_days = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->where('visit_date', '<', Carbon::now()->subDays(30))
            ->count();

        $visited_more_90_days = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->where('visit_date', '<', Carbon::now()->subDays(90))
            ->count();

        /** For Pie Chart */
        $visits_cancel_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->whereMonth('customer_visits.created_at', $currentMonth) //get data current month 11,12 etc
            ->where('customer_visits.status', '=', 'Cancelado')
            ->count();

        $visits_process_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->whereMonth('customer_visits.created_at', $currentMonth) //get data current month 11,12 etc
            ->where('customer_visits.status', '=', 'Procesado')
            ->count();

        $visits_no_process_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->whereMonth('customer_visits.created_at', $currentMonth) //get data current month 11,12 etc
            ->where('customer_visits.status', '=', 'No Procesado')
            ->count();

        $visits_pending_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->whereMonth('customer_visits.created_at', $currentMonth) //get data current month 11,12 etc
            ->where('customer_visits.status', '=', 'Pendiente')
            ->count();

        $sales_count = DB::table('sales')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->whereMonth('sales.created_at', $currentMonth) //get data current month 11,12 etc
            ->where('sales.previous_type', '=', 'Venta')
            ->where('sales.status', '=', 'Procesado')
            ->count();

        $orders_count = DB::table('sales')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->whereMonth('sales.created_at', $currentMonth) //get data current month 11,12 etc
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
            ->where('sales.type', '=', 'Presupuesto')
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

        return view('user::dashboard', compact(
            'currentMonthName',
            'customer_visits',
            'cant_customers',
            'sales',
            'currentDate',
            'currentOnlyYear',
            'visited_less_30_days',
            'visited_more_30_days',
            'visited_more_90_days',
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
