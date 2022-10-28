<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
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
        $categories = DB::table('parameters')
            ->where('type', '=', 'Rubro')
            ->select('id', 'name')
            ->get();

        $status = [
            'Procesado',
            'No Procesado',
            'Cancelado'
        ];

        $visits_labels = [
            'Menos de 30 días',
            'Más de 30 días',
            'Más de 90 días'
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

        $potential_products = DB::table('parameters')
            ->where('type', '=', 'Equipos Potenciales')
            ->get();

        return view('user::whatdo.index', compact('categories', 'potential_products', 'estates', 'status', 'visits_labels'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function visit_on_map()
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
                'customer_visits.next_visit_hour',
                'customer_visits.status',
                'customer_visits.type',
                'customer_visits.action',
                'customers.id AS customer_id',
                'customers.name AS customer_name',
                'customers.city',
                'customers.estate',
                'customers.latitude',
                'customers.longitude',
            )
            ->groupBy('customers.id')
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
                    'customer_visits.visit_number',
                    'customer_visits.visit_date',
                    'customer_visits.next_visit_date',
                    'customer_visits.status',
                    'customer_visits.type',
                    'customers.name AS customer_name',
                    'customers.estate',
                    'customers.category'
                )
                ->orderBy('customer_visits.created_at', 'DESC')
                ->get();
        } else {

            if ($type == 'estate') {
                $customer_visits = DB::table('customer_visits')
                    ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                    ->where('customers.estate', 'LIKE', "%{$filter}%")
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
                        'customers.category'
                    )
                    ->orderBy('customer_visits.created_at', 'DESC')
                    ->get();
            } elseif ($type == 'status') {
                $customer_visits = DB::table('customer_visits')
                    ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                    ->where('customer_visits.status', 'LIKE', "{$filter}%")
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
                        'customers.category'
                    )
                    ->orderBy('customer_visits.created_at', 'DESC')
                    ->get();
            } elseif ($type == 'category') {
                $customer_visits = DB::table('customer_visits')
                    ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                    ->leftjoin('customer_parameters', 'customer_parameters.customer_id', '=', 'customers.id')
                    ->where('customer_parameters.category_id', '=', $filter)
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
                        'customers.category'
                    )
                    ->orderBy('customer_visits.created_at', 'DESC')
                    ->get();
            } elseif ($type == 'potential_products') {
                $customer_visits = DB::table('customer_visits')
                    ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                    ->leftjoin('customer_parameters', 'customer_parameters.customer_id', '=', 'customers.id')
                    ->where('customer_parameters.potential_product_id', '=', $filter)
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
                        'customers.category'
                    )
                    ->orderBy('customer_visits.created_at', 'DESC')
                    ->get();

                // } elseif ($type == 'visit_date') {
                //     $customer_visits = DB::table('customer_visits')
                //         ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                //         ->where('customer_visits.visit_date', 'LIKE', "{$filter}%")
                //         ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
                //         ->select(
                //             'customer_visits.id',
                //             'customer_visits.visit_number',
                //             'customer_visits.visit_date',
                //             'customer_visits.next_visit_date',
                //             'customer_visits.status',
                //             'customer_visits.type',
                //             'customers.name AS customer_name',
                //             'customers.estate',
                //             'customers.category'
                //         )
                //         ->orderBy('customer_visits.created_at', 'DESC')
                //         ->get();
            } elseif ($type == 'visit_date') {

                if ($filter == 'Menos de 30 días') {
                    $customer_visits = DB::table('customer_visits')
                        ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                        ->where('customers.idReference', '=', $idRefCurrentUser)
                        ->where('visit_date', '>', Carbon::now()->subDays(30))
                        ->select(
                            'customer_visits.id',
                            'customer_visits.visit_number',
                            'customer_visits.visit_date',
                            'customer_visits.next_visit_date',
                            'customer_visits.status',
                            'customer_visits.type',
                            'customers.name AS customer_name',
                            'customers.estate',
                            'customers.category'
                        )
                        ->orderBy('customer_visits.created_at', 'DESC')
                        ->get();
                }

                if ($filter == 'Más de 30 días') {
                    $customer_visits = DB::table('customer_visits')
                        ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                        ->where('customers.idReference', '=', $idRefCurrentUser)
                        ->where('visit_date', '<', Carbon::now()->subDays(30))
                        ->select(
                            'customer_visits.id',
                            'customer_visits.visit_number',
                            'customer_visits.visit_date',
                            'customer_visits.next_visit_date',
                            'customer_visits.status',
                            'customer_visits.type',
                            'customers.name AS customer_name',
                            'customers.estate',
                            'customers.category'
                        )
                        ->orderBy('customer_visits.created_at', 'DESC')
                        ->get();
                }

                if ($filter == 'Más de 90 días') {
                    $customer_visits = DB::table('customer_visits')
                        ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                        ->where('customers.idReference', '=', $idRefCurrentUser)
                        ->where('visit_date', '<', Carbon::now()->subDays(90))
                        ->select(
                            'customer_visits.id',
                            'customer_visits.visit_number',
                            'customer_visits.visit_date',
                            'customer_visits.next_visit_date',
                            'customer_visits.status',
                            'customer_visits.type',
                            'customers.name AS customer_name',
                            'customers.estate',
                            'customers.category'
                        )
                        ->orderBy('customer_visits.created_at', 'DESC')
                        ->get();
                }
            } elseif ($type == 'next_visit_date') {

                $customer_visits = DB::table('customer_visits')
                    ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                    ->where('customer_visits.next_visit_date', 'LIKE', "{$filter}%")
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
                        'customers.category'
                    )
                    ->orderBy('customer_visits.created_at', 'DESC')
                    ->get();
            }
        }

        $categories = DB::table('parameters')
            ->where('type', '=', 'Rubro')
            ->select('id', 'name')
            ->get();

        return View::make('user::whatdo._partials.datatable', compact('customer_visits', 'filter', 'categories'));
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
                    'customer_visits.visit_number',
                    'customer_visits.visit_date',
                    'customer_visits.next_visit_date',
                    'customer_visits.status',
                    'customer_visits.type',
                    'customers.name AS customer_name',
                    'customers.estate',
                    'customers.category'
                )
                ->orderBy('customer_visits.created_at', 'DESC')
                ->get();
        } else {
            $customer_visits = DB::table('customer_visits')
                ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                ->where('customers.idReference', '=', $idRefCurrentUser)
                ->where('customers.name', 'LIKE', "%{$search}%")
                ->select(
                    'customer_visits.id',
                    'customer_visits.visit_number',
                    'customer_visits.visit_date',
                    'customer_visits.next_visit_date',
                    'customer_visits.next_visit_hour',
                    'customer_visits.result_of_the_visit',
                    'customer_visits.objective',
                    'customer_visits.status',
                    'customer_visits.type',
                    'customers.name AS customer_name',
                    'customers.estate',
                    'customers.category'
                )
                ->orderBy('customer_visits.created_at', 'DESC')
                ->get();
        }

        $customers_categories = DB::table('parameters')
            ->where('type', '=', 'Rubro')
            ->pluck('name', 'name');

        $status = [
            'Procesado',
            'No Procesado',
            'Cancelado'
        ];

        $visits_labels = [
            'Menos de 30 días',
            'Más de 30 días',
            'Más de 90 días'
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

        $categories = DB::table('parameters')
            ->where('type', '=', 'Rubro')
            ->select('id', 'name')
            ->get();

        return View::make('user::whatdo._partials.datatable', compact('customer_visits', 'search', 'categories'));
    }
}
