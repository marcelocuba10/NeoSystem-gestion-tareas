<?php

namespace Modules\User\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeApiController extends Controller
{

    public function index($idRefCurrentUser)
    {
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
            ->where('sales.type', '=', 'Venta')
            ->where('sales.status', '=', 'Procesado')
            ->count();

        $orders_count = DB::table('sales')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->whereMonth('sales.created_at', $currentMonth) //get data current month 11,12 etc
            ->where('sales.previous_type', '=', 'Presupuesto')
            ->where('sales.status', '!=', 'Cancelado')
            ->count();

        return response()->json(array(
            'visited_less_30_days' => $visited_less_30_days,
            'visited_more_30_days' => $visited_more_30_days,
            'visited_more_90_days' => $visited_more_90_days,
            'customer_visits' => $customer_visits,
            'cant_customers' => $cant_customers,
            'sales_count' => $sales_count,
            'orders_count' => $orders_count,
        ));
    }
}
