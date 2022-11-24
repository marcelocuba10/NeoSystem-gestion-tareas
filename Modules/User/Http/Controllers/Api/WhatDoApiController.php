<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Modules\User\Entities\Customers;
use Modules\User\Entities\CustomerVisit;

class WhatDoApiController extends Controller
{
    public function getCustomerVisits($idRefCurrentUser)
    {
        $customer_visits = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
            ->select(
                'customer_visits.id',
                'customer_visits.visit_number',
                'customer_visits.customer_id',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.next_visit_hour',
                'customer_visits.result_of_the_visit',
                'customer_visits.objective',
                'customer_visits.action',
                'customer_visits.type',
                'customer_visits.status',
                'customers.name AS customer_name',
                'customers.estate',
                'customers.latitude',
                'customers.longitude',
            )
            ->orderBy('customer_visits.created_at', 'DESC')
            ->get();

        $categories = DB::table('parameters')
            ->where('type', '=', 'Rubro')
            ->select('id', 'name')
            ->get();

        $potential_products = DB::table('parameters')
            ->where('type', '=', 'Equipos Potenciales')
            ->select('id', 'name')
            ->get();

        $customers = DB::table('customers')
            ->where('idReference', '=', $idRefCurrentUser)
            ->where('customers.status', '=', 1)
            ->orderBy('created_at', 'DESC')
            ->get();

        $actions = [
            'Realizar Llamada',
            'Realizar Visita',
            'Enviar Presupuesto'
        ];

        $status = [
            'Pendiente',
            'Procesado',
            'No Procesado',
            'Cancelado'
        ];

        $visits_labels = [
            'Menos de 30 días',
            'Más de 30 días',
            'Más de 90 días'
        ];

        return response()->json(array(
            'customer_visits' => $customer_visits,
            'categories' => $categories,
            'potential_products' => $potential_products,
            'status' => $status,
            'visits_labels' => $visits_labels,
            'customers' => $customers,
            'actions' => $actions
        ));
    }

    public function visit_on_map($idRefCurrentUser)
    {
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
                'customer_visits.customer_id',
                'customers.name AS customer_name',
                'customers.city',
                'customers.latitude',
                'customers.longitude',
            )
            //->groupBy('customer_visits.customer_id')
            ->orderBy('customer_visits.visit_date', 'ASC')
            ->get();

        return response()->json(array(
            'customer_visits' => $customer_visits,
        ));
    }

    public function filter($filter, $type, $idRefCurrentUser)
    {
        if ($filter == '') {
            $customer_visits = DB::table('customer_visits')
                ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
                ->select(
                    'customer_visits.id',
                    'customer_visits.visit_number',
                    'customer_visits.customer_id',
                    'customer_visits.visit_date',
                    'customer_visits.next_visit_date',
                    'customer_visits.next_visit_hour',
                    'customer_visits.result_of_the_visit',
                    'customer_visits.objective',
                    'customer_visits.action',
                    'customer_visits.type',
                    'customer_visits.status',
                    'customers.name AS customer_name',
                    'customers.estate',
                    'customers.category',
                )
                ->orderBy('customer_visits.created_at', 'DESC')
                ->get();
        } else {
            if ($type == 'status') {
                $customer_visits = DB::table('customer_visits')
                    ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                    ->where('customer_visits.status', 'LIKE', "{$filter}%")
                    ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
                    ->select(
                        'customer_visits.id',
                        'customer_visits.visit_number',
                        'customer_visits.customer_id',
                        'customer_visits.visit_date',
                        'customer_visits.next_visit_date',
                        'customer_visits.next_visit_hour',
                        'customer_visits.result_of_the_visit',
                        'customer_visits.objective',
                        'customer_visits.action',
                        'customer_visits.type',
                        'customer_visits.status',
                        'customers.name AS customer_name',
                        'customers.estate',
                        'customers.category',
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
                        'customer_visits.customer_id',
                        'customer_visits.visit_date',
                        'customer_visits.next_visit_date',
                        'customer_visits.next_visit_hour',
                        'customer_visits.result_of_the_visit',
                        'customer_visits.objective',
                        'customer_visits.action',
                        'customer_visits.type',
                        'customer_visits.status',
                        'customers.name AS customer_name',
                        'customers.estate',
                        'customers.category',
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
                        'customer_visits.customer_id',
                        'customer_visits.visit_date',
                        'customer_visits.next_visit_date',
                        'customer_visits.next_visit_hour',
                        'customer_visits.result_of_the_visit',
                        'customer_visits.objective',
                        'customer_visits.action',
                        'customer_visits.type',
                        'customer_visits.status',
                        'customers.name AS customer_name',
                        'customers.estate',
                        'customers.category',
                    )
                    ->orderBy('customer_visits.created_at', 'DESC')
                    ->get();
            } elseif ($type == 'visit_date') {

                if ($filter == 'Menos de 30 días') {
                    $customer_visits = DB::table('customer_visits')
                        ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                        ->where('customers.idReference', '=', $idRefCurrentUser)
                        ->where('visit_date', '>', Carbon::now()->subDays(30)) //Laravel Carbon subtract days from current date
                        ->select(
                            'customer_visits.id',
                            'customer_visits.visit_number',
                            'customer_visits.customer_id',
                            'customer_visits.visit_date',
                            'customer_visits.next_visit_date',
                            'customer_visits.next_visit_hour',
                            'customer_visits.result_of_the_visit',
                            'customer_visits.objective',
                            'customer_visits.action',
                            'customer_visits.type',
                            'customer_visits.status',
                            'customers.name AS customer_name',
                            'customers.estate',
                            'customers.category',
                        )
                        ->orderBy('customer_visits.created_at', 'DESC')
                        ->get();
                }

                if ($filter == 'Más de 30 días') {
                    $customer_visits = DB::table('customer_visits')
                        ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                        ->where('customers.idReference', '=', $idRefCurrentUser)
                        ->where('visit_date', '<', Carbon::now()->subDays(30))
                        ->where('visit_date', '>', Carbon::now()->subDays(90))
                        ->select(
                            'customer_visits.id',
                            'customer_visits.visit_number',
                            'customer_visits.customer_id',
                            'customer_visits.visit_date',
                            'customer_visits.next_visit_date',
                            'customer_visits.next_visit_hour',
                            'customer_visits.result_of_the_visit',
                            'customer_visits.objective',
                            'customer_visits.action',
                            'customer_visits.type',
                            'customer_visits.status',
                            'customers.name AS customer_name',
                            'customers.estate',
                            'customers.category',
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
                            'customer_visits.customer_id',
                            'customer_visits.visit_date',
                            'customer_visits.next_visit_date',
                            'customer_visits.next_visit_hour',
                            'customer_visits.result_of_the_visit',
                            'customer_visits.objective',
                            'customer_visits.action',
                            'customer_visits.type',
                            'customer_visits.status',
                            'customers.name AS customer_name',
                            'customers.estate',
                            'customers.category',
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
                        'customer_visits.customer_id',
                        'customer_visits.visit_date',
                        'customer_visits.next_visit_date',
                        'customer_visits.next_visit_hour',
                        'customer_visits.result_of_the_visit',
                        'customer_visits.objective',
                        'customer_visits.action',
                        'customer_visits.type',
                        'customer_visits.status',
                        'customers.name AS customer_name',
                        'customers.estate',
                        'customers.category',
                    )
                    ->orderBy('customer_visits.created_at', 'DESC')
                    ->get();
            }
        }

        $categories = DB::table('parameters')
            ->where('type', '=', 'Rubro')
            ->select('id', 'name')
            ->get();

        $potential_products = DB::table('parameters')
            ->where('type', '=', 'Equipos Potenciales')
            ->select('id', 'name')
            ->get();

        $customers = DB::table('customers')
            ->where('idReference', '=', $idRefCurrentUser)
            ->where('customers.status', '=', 1)
            ->orderBy('created_at', 'DESC')
            ->get();

        $actions = [
            'Realizar Llamada',
            'Realizar Visita',
            'Enviar Presupuesto'
        ];

        $status = [
            'Pendiente',
            'Procesado',
            'No Procesado',
            'Cancelado'
        ];

        $visits_labels = [
            'Menos de 30 días',
            'Más de 30 días',
            'Más de 90 días'
        ];

        return response()->json(array(
            'customer_visits' => $customer_visits,
            'categories' => $categories,
            'potential_products' => $potential_products,
            'status' => $status,
            'visits_labels' => $visits_labels,
            'customers' => $customers,
            'actions' => $actions
        ));
    }
}
