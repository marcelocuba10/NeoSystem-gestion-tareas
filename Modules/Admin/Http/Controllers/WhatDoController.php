<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class WhatDoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['logout']]);

        $this->middleware('permission:report-sa-list|report-sa-create|report-sa-edit|report-sa-delete', ['only' => ['index']]);
        $this->middleware('permission:report-sa-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:report-sa-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:report-sa-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $customer_visits = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->leftjoin('customer_parameters', 'customer_parameters.customer_id', '=', 'customers.id')
            ->leftjoin('parameters', 'parameters.id', '=', 'customer_parameters.category_id')
            ->where('customer_parameters.category_id', '!=', 0)
            ->select(
                'customer_visits.id',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.status',
                'customer_visits.type',
                'customers.id AS customer_id',
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

        return view('admin::whatdo.index', compact('customer_visits', 'categories', 'potential_products', 'estates', 'status', 'visits_labels'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function visit_on_map()
    {
        $customer_visits = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->select(
                'customer_visits.id',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.next_visit_hour',
                'customer_visits.status',
                'customer_visits.type',
                'customers.id AS customer_id',
                'customers.name AS customer_name',
                'customers.city',
                'customers.estate',
                'customers.latitude',
                'customers.longitude',
            )
            ->orderBy('customer_visits.created_at', 'DESC')
            ->get();

        return view('admin::whatdo.visits_on_map', compact('customer_visits',));
    }

    public function filter(Request $request)
    {
        $filter = $request->input('filter');
        $type = $request->input('type');

        if ($filter == '') {
            $customer_visits = DB::table('customer_visits')
                ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
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
                    ->leftjoin('customer_parameters', 'customer_parameters.customer_id', '=', 'customers.id')
                    ->where('customer_parameters.potential_product_id', '=', $filter)
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

                // } elseif ($type == 'visit_date') {
                //     $customer_visits = DB::table('customer_visits')
                //         ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                //         ->where('customer_visits.visit_date', 'LIKE', "{$filter}%")
                //         ->select(
                //             'customer_visits.id',
                //             'customer_visits.visit_date',
                //             'customer_visits.next_visit_date',
                //             'customer_visits.status',
                //             'customer_visits.type',
                //             'customers.name AS customer_name',
                //             'customers.estate',
                //             'customers.category'
                //         )
                //         ->orderBy('customer_visits.created_at', 'DESC')
                //         ->paginate(10);
            } elseif ($type == 'visit_date') {

                if ($filter == 'Menos de 30 días') {
                    $customer_visits = DB::table('customer_visits')
                        ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                        ->where('visit_date', '>', Carbon::now()->subDays(30))
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

                if ($filter == 'Más de 30 días') {
                    $customer_visits = DB::table('customer_visits')
                        ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                        ->where('visit_date', '<', Carbon::now()->subDays(30))
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

                if ($filter == 'Más de 90 días') {
                    $customer_visits = DB::table('customer_visits')
                        ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                        ->where('visit_date', '<', Carbon::now()->subDays(90))
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
            } elseif ($type == 'next_visit_date') {

                $customer_visits = DB::table('customer_visits')
                    ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                    ->where('customer_visits.next_visit_date', 'LIKE', "{$filter}%")
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

        return View::make('admin::whatdo._partials.datatable', compact('customer_visits', 'filter', 'categories'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        if ($search == '') {
            $customer_visits = DB::table('customer_visits')
                ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
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
            $customer_visits = DB::table('customer_visits')
                ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
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
                    'customers.estate',
                    'customers.category'
                )
                ->orderBy('customer_visits.created_at', 'DESC')
                ->paginate();
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

        return View::make('admin::whatdo._partials.datatable', compact('customer_visits', 'search', 'categories'))->with('i', (request()->input('page', 1) - 1) * 10);
    }
}
