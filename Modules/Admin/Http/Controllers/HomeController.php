<?php

namespace Modules\Admin\Http\Controllers;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['logout']]);
    }

    public function index()
    {
        $currentDate = Carbon::now()->format('d/m/Y');
        $currentOnlyYear = Carbon::now()->format('Y');

        $currentMonth = Carbon::now()->format('m');

        Carbon::setlocale('ES');
        $currentMonthName = Carbon::parse(Carbon::now()->format('Y/m/d'))->translatedFormat('F');

        $customer_visits = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->select(
                'customer_visits.id',
                'customer_visits.visit_number',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.status',
                'customer_visits.type',
                'customers.name AS customer_name',
                'customers.estate',
                'customers.phone',
            )
            ->orderBy('customer_visits.created_at', 'DESC')
            ->paginate(7);

        $cant_customers = DB::table('customers')
            ->count();

        $cant_sellers = DB::table('users')
            ->count();

        $appointments = DB::table('appointments')
            ->leftjoin('customers', 'customers.id', '=', 'appointments.customer_id')
            ->leftjoin('customer_visits', 'customer_visits.id', '=', 'appointments.visit_id')
            ->select(
                'appointments.id',
                'customers.name AS customer_name',
                'customers.phone AS customer_phone',
                'customers.estate AS customer_estate',
                'appointments.date',
                'appointments.hour',
                'appointments.action',
                'appointments.observation',
            )
            ->orderBy('appointments.created_at', 'DESC')
            ->paginate(5);

        /** For info cards */
        $visited_less_30_days = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('visit_date', '>', Carbon::now()->subDays(30))
            ->count();

        $visited_more_30_days = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('visit_date', '<', Carbon::now()->subDays(30))
            ->count();

        $visited_more_90_days = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('visit_date', '<', Carbon::now()->subDays(90))
            ->count();

        /** For Pie Chart */
        $visits_cancel_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->whereMonth('customer_visits.created_at', $currentMonth) //get data current month 11,12 etc
            ->where('customer_visits.status', '=', 'Cancelado')
            ->count();

        $visits_process_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->whereMonth('customer_visits.created_at', $currentMonth) //get data current month 11,12 etc
            ->where('customer_visits.status', '=', 'Procesado')
            ->count();

        $visits_no_process_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->whereMonth('customer_visits.created_at', $currentMonth) //get data current month 11,12 etc
            ->where('customer_visits.status', '=', 'No Procesado')
            ->count();

        $visits_pending_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->whereMonth('customer_visits.created_at', $currentMonth) //get data current month 11,12 etc
            ->where('customer_visits.status', '=', 'Pendiente')
            ->count();

        $sales_count = DB::table('sales')
            ->where('previous_type', '=', 'Venta')
            ->whereMonth('sales.created_at', $currentMonth) //get data current month 11,12 etc
            ->where('sales.previous_type', '=', 'Venta')
            ->where('sales.status', '=', 'Procesado')
            ->count();

        $orders_count = DB::table('sales')
            ->where('previous_type', '=', 'Presupuesto')
            ->whereMonth('sales.created_at', $currentMonth) //get data current month 11,12 etc
            ->where('sales.previous_type', '=', 'Presupuesto')
            ->where('sales.status', '!=', 'Cancelado')
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

        return view('admin::dashboard', compact(
            'currentMonthName',
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
            'customer_visits',
            'cant_customers',
            'cant_sellers',
            'appointments',
            'currentDate',
            'currentOnlyYear',
            'visited_less_30_days',
            'visited_more_30_days',
            'visited_more_90_days'
        ));
    }
}
