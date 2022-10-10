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

        return view('user::whatdo.index', compact('customer_visits', 'customers_categories', 'estates', 'status'))->with('i', (request()->input('page', 1) - 1) * 10);
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

                // $latitude = json_encode($customer_visits);
                // dd($customer_visits[0]->latitude);
                // $latitude = str_replace('"', '', $latitude);
                // $latitude = doubleval($latitude);
                // dd($latitude);

                // dd($customer_visits);

            $locations = [
                ['Mumbai', 19.0760,72.8777],
                ['Pune', 18.5204,73.8567],
                ['Bhopal ', 23.2599,77.4126],
                ['Agra', 27.1767,78.0081],
                ['Delhi', 28.7041,77.1025],
                ['Rajkot', 22.2734719,70.7512559],
            ];

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
                    'customers.estate'
                )
                ->orderBy('customer_visits.created_at', 'DESC')
                ->paginate(10);
        } else {

            if ($type == 'Localidad') {
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
                        'customers.estate'
                    )
                    ->orderBy('customer_visits.created_at', 'DESC')
                    ->paginate(10);
            } elseif ($type == 'Estado') {
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
            } elseif ($type == 'Rubro') {

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
            } elseif ($type == 'Ultima Visita') {

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
            } elseif ($type == 'Próxima Visita') {

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

        return View::make('user::whatdo._partials.datatable', compact('customer_visits', 'filter'))->with('i', (request()->input('page', 1) - 1) * 10);
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
