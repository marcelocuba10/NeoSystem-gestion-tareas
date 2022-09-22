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

        $customers = DB::table('customers')
            ->select('customers.id', 'customers.name', 'customers.phone', 'customers.address', 'customers.doc_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->orderBy('customers.created_at', 'DESC')
            ->limit(7)
            ->get();

        $products = DB::table('products')
            ->select('id', 'name', 'quantity')
            ->orderBy('created_at', 'DESC')
            ->paginate(7);

        $cant_customers = DB::table('customers')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->count();

        $cant_products = DB::table('products')
            ->count();

        return view('user::dashboard', compact('products', 'customers', 'cant_customers', 'cant_products'));
    }
}
