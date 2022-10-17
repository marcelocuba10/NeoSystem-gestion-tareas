<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Modules\User\Entities\Customers;
use Modules\User\Entities\CustomerVisit;

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
            ->leftjoin('customer_parameters', 'customer_parameters.customer_id', '=', 'customers.id')
            ->leftjoin('parameters', 'parameters.id', '=', 'customer_parameters.category_id')
            ->where('customer_parameters.category_id', '!=', 0)
            ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
            ->select(
                'customer_visits.id',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.status',
                'customer_visits.type',
                'customers.name AS customer_name',
                'customers.estate',
                'customers.category',
                'parameters.name'
            )
            ->orderBy('customer_visits.created_at', 'DESC')
            ->paginate(10); 

        $categories = DB::table('parameters')
            ->where('type', '=', 'Rubro')
            ->select('id', 'name')
            ->get();

        $potential_products = DB::table('parameters')
            ->where('type', '=', 'Equipos Potenciales')
            ->pluck('name', 'name');

        $status = [
            'Visitado',
            'No Visitado',
            'Cancelado'
        ];

        $estates = [
            'Alto Paraná',
            'Central',
            'Concepción',
            'San Pedro',
            'Cordillera',
            'Guairá',
            'Caaguazú',
            'Caazapá',
            'Itapúa',
            'Misiones',
            'Paraguarí',
            'Ñeembucú',
            'Amambay',
            'Canindeyú',
            'Presidente Hayes',
            'Boquerón',
            'Alto Paraguay'
        ];

        return view('user::whatdo.index', compact('customer_visits', 'categories', 'potential_products', 'estates', 'status'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function visit_on_map()
    {
        $idRefCurrentUser = Auth::user()->idReference;
        $customer_visits = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
            ->select(
                'customer_visits.id',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.next_visit_hour',
                'customer_visits.status',
                'customer_visits.type',
                'customers.name AS customer_name',
                'customers.city',
                'customers.estate',
                'customers.latitude',
                'customers.longitude',
            )
            ->orderBy('customer_visits.created_at', 'DESC')
            ->get();

        return view('user::whatdo.visits_on_map', compact('customer_visits',));
    }

    public function filter(Request $request)
    {
        $filter = $request->input('filter');
        $type = $request->input('type');
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
                    'customers.estate',
                    'customers.category'
                )
                ->orderBy('customer_visits.created_at', 'DESC')
                ->paginate(10);
        } else {

            if ($type == 'estate') {
                $customer_visits = DB::table('customer_visits')
                    ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                    ->where('customers.estate', 'LIKE', "%{$filter}%")
                    ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
                    ->select(
                        'customer_visits.id',
                        'customer_visits.visit_date',
                        'customer_visits.next_visit_date',
                        'customer_visits.status',
                        'customer_visits.type',
                        'customers.name AS customer_name',
                        'customers.estate',
                        'customers.category'
                    )
                    ->orderBy('customer_visits.created_at', 'DESC')
                    ->paginate(10);
            } elseif ($type == 'status') {
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
                        'customers.estate',
                        'customers.category'
                    )
                    ->orderBy('customer_visits.created_at', 'DESC')
                    ->paginate(10);
            } elseif ($type == 'category') {

                $customer_visits = DB::table('customer_visits')
                    ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                    ->leftjoin('customer_parameters', 'customer_parameters.customer_id', '=', 'customers.id')
                    ->where('customer_parameters.category_id', '=', $filter)
                    ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
                    ->select(
                        'customer_visits.id',
                        'customer_visits.visit_date',
                        'customer_visits.next_visit_date',
                        'customer_visits.status',
                        'customer_visits.type',
                        'customers.name AS customer_name',
                        'customers.estate',
                        'customers.category'
                    )
                    ->orderBy('customer_visits.created_at', 'DESC')
                    ->paginate(10);
            } elseif ($type == 'potential_products') {

                $customer_visits = DB::table('customer_visits')
                    ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                    ->leftjoin('parameters', 'parameters.name', '=', 'customers.category')
                    ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
                    ->select(
                        'customer_visits.id',
                        'customer_visits.visit_date',
                        'customer_visits.next_visit_date',
                        'customer_visits.status',
                        'customer_visits.type',
                        'customers.name AS customer_name',
                        'customers.estate',
                        'customers.category'
                    )
                    ->orderBy('customer_visits.created_at', 'DESC')
                    ->paginate(10);

                dd($customer_visits);
            } elseif ($type == 'visit_date') {

                $customer_visits = DB::table('customer_visits')
                    ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                    ->where('customer_visits.visit_date', 'LIKE', "{$filter}%")
                    ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
                    ->select(
                        'customer_visits.id',
                        'customer_visits.visit_date',
                        'customer_visits.next_visit_date',
                        'customer_visits.status',
                        'customer_visits.type',
                        'customers.name AS customer_name',
                        'customers.estate',
                        'customers.category'
                    )
                    ->orderBy('customer_visits.created_at', 'DESC')
                    ->paginate(10);
            } elseif ($type == 'next_visit_date') {

                $customer_visits = DB::table('customer_visits')
                    ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                    ->where('customer_visits.next_visit_date', 'LIKE', "{$filter}%")
                    ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
                    ->select(
                        'customer_visits.id',
                        'customer_visits.visit_date',
                        'customer_visits.next_visit_date',
                        'customer_visits.status',
                        'customer_visits.type',
                        'customers.name AS customer_name',
                        'customers.estate',
                        'customers.category'
                    )
                    ->orderBy('customer_visits.created_at', 'DESC')
                    ->paginate(10);
            }
        }

        $categories = DB::table('parameters')
            ->where('type', '=', 'Rubro')
            ->select('id', 'name')
            ->get();

        $status = [
            'Visitado',
            'No Atendido',
            'Cancelado'
        ];

        $estates = [
            'Alto Paraná',
            'Central',
            'Concepción',
            'San Pedro',
            'Cordillera',
            'Guairá',
            'Caaguazú',
            'Caazapá',
            'Itapúa',
            'Misiones',
            'Paraguarí',
            'Ñeembucú',
            'Amambay',
            'Canindeyú',
            'Presidente Hayes',
            'Boquerón',
            'Alto Paraguay'
        ];

        return View::make('user::whatdo._partials.datatable', compact('customer_visits', 'filter', 'categories'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $idRefCurrentUser = Auth::user()->idReference;

        if ($search == '') {
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

        $customers_categories = DB::table('parameters')
            ->where('type', '=', 'Rubro')
            ->pluck('name', 'name');

        $status = [
            'Visitado',
            'No Atendido',
            'Cancelado'
        ];

        $estates = [
            'Alto Paraná',
            'Central',
            'Concepción',
            'San Pedro',
            'Cordillera',
            'Guairá',
            'Caaguazú',
            'Caazapá',
            'Itapúa',
            'Misiones',
            'Paraguarí',
            'Ñeembucú',
            'Amambay',
            'Canindeyú',
            'Presidente Hayes',
            'Boquerón',
            'Alto Paraguay'
        ];

        return View::make('user::whatdo._partials.datatable', compact('customer_visits', 'search'))->with('i', (request()->input('page', 1) - 1) * 10);
    }
}
