<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
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
        $idRefCurrentUser = Auth::user()->idReference;

        $sellers = DB::table('users')
            ->select('id', 'name', 'last_name', 'idReference', 'status')
            ->where('main_user', '=', 1)
            ->orderBy('created_at', 'DESC')
            ->limit(7)
            ->get();

        $products = DB::table('products')
            ->select('id', 'name', 'inventory')
            ->orderBy('created_at', 'DESC')
            ->limit(7)
            ->get();

        $cant_sellers = DB::table('users')
            ->where('main_user', '=', 1)
            ->count();

        $cant_products = DB::table('products')
            ->count();

        return view('admin::dashboard', compact('products', 'sellers', 'cant_sellers', 'cant_products'));
    }
}
