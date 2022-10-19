<?php

namespace Modules\User\Http\Controllers;

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

        $cant_products = DB::table('products')
            ->count();

        $total_sales = DB::table('sales')
            ->where('sales.seller_id', '=', $idRefCurrentUser)
            ->where('sales.type', '=', 'Sale')
            ->sum('total');

        $total_visits = DB::table('customer_visits')
            ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
            ->count();

        return view('user::dashboard', compact('customer_visits', 'cant_customers', 'cant_products', 'total_sales', 'total_visits'));
    }
}
