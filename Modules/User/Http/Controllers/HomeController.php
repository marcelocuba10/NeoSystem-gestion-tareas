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

        $customer_visits = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
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
            ->paginate(5);

        $cant_customers = DB::table('customers')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->count();

        $appointments = DB::table('appointments')
            ->leftjoin('customers', 'customers.id', '=', 'appointments.customer_id')
            ->where('appointments.idReference', '=', $idRefCurrentUser)
            ->select(
                'appointments.id',
                'customers.name AS customer_name',
                'customers.phone AS customer_phone',
                'customers.estate AS customer_estate',
                'date',
                'hour',
                'action',
                'observation',
            )
            ->orderBy('appointments.created_at', 'DESC')
            ->paginate(5);

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

        return view('user::dashboard', compact(
            'customer_visits',
            'cant_customers',
            'appointments',
            'currentDate',
            'visited_less_30_days',
            'visited_more_30_days',
            'visited_more_90_days'
        ));
    }
}
