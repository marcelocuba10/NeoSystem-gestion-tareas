<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WhatDoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web', ['except' => ['logout']]);

        $this->middleware('permission:what_can_do-list|what_can_do-create|what_can_do-edit|what_can_do-delete', ['only' => ['index']]);
        $this->middleware('permission:what_can_do-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:what_can_do-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:what_can_do-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $idRefCurrentUser = Auth::user()->idReference;
        $customer_visits = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
            ->select(
                'customer_visits.id',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.status',
                'customer_visits.type',
                'customers.name AS customer_name',
                'customers.estate'
            )
            ->orderBy('customer_visits.created_at', 'DESC')
            ->paginate(10);

        return view('user::whatdo.index', compact('customer_visits'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function filter(Request $request)
    {
        $filter = $request->input('filter');
        $idRefCurrentUser = Auth::user()->idReference;

        if ($filter == '') {
            $customer_visits = DB::table('customer_visits')
                ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
                ->select(
                    'customer_visits.id',
                    'customer_visits.visit_date',
                    'customer_visits.next_visit_date',
                    'customer_visits.status',
                    'customer_visits.type',
                    'customers.name AS customer_name',
                    'customers.estate'
                )
                ->orderBy('customer_visits.created_at', 'DESC')
                ->paginate(10);
        } else {
            $customer_visits = DB::table('customer_visits')
                ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                ->where('customer_visits.status', 'LIKE', "%{$filter}%")
                ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
                ->select(
                    'customer_visits.id',
                    'customer_visits.visit_date',
                    'customer_visits.next_visit_date',
                    'customer_visits.status',
                    'customer_visits.type',
                    'customers.name AS customer_name',
                    'customers.estate'
                )
                ->orderBy('customer_visits.created_at', 'DESC')
                ->paginate(10);
        }

        return view('user::whatdo.index', compact('customer_visits', 'filter'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $idRefCurrentUser = Auth::user()->idReference;

        if ($search == '') {
            $customer_visits = DB::table('customer_visits')
                ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                ->where('customers.idReference', '=', $idRefCurrentUser)
                ->select(
                    'customer_visits.id',
                    'customer_visits.visit_date',
                    'customer_visits.next_visit_date',
                    'customer_visits.next_visit_hour',
                    'customer_visits.result_of_the_visit',
                    'customer_visits.objective',
                    'customer_visits.status',
                    'customer_visits.type',
                    'customers.name AS customer_name',
                    'customers.estate'
                )
                ->orderBy('customer_visits.created_at', 'DESC')
                ->paginate(10);
        } else {
            $customer_visits = DB::table('customer_visits')
                ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                ->where('customers.idReference', '=', $idRefCurrentUser)
                ->where('customers.name', 'LIKE', "%{$search}%")
                ->select(
                    'customer_visits.id',
                    'customer_visits.visit_date',
                    'customer_visits.next_visit_date',
                    'customer_visits.next_visit_hour',
                    'customer_visits.result_of_the_visit',
                    'customer_visits.objective',
                    'customer_visits.status',
                    'customer_visits.type',
                    'customers.name AS customer_name',
                    'customers.estate'
                )
                ->orderBy('customer_visits.created_at', 'DESC')
                ->paginate();
        }

        return view('user::whatdo.index', compact('customer_visits', 'search'))->with('i', (request()->input('page', 1) - 1) * 10);
    }
}
