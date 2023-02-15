<?php

namespace Modules\User\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\Appointment;
use Modules\User\Entities\CustomerParameters;
use Modules\User\Entities\Customers;
use Modules\User\Entities\CustomerVisit;
use Modules\User\Entities\OrderDetail;
use Modules\User\Entities\Sales;

use PDF;
use Mail;
use Modules\User\Emails\NotifyMail;

class SalesApiController extends Controller
{

    public function index($idRefCurrentUser)
    {
        $sales = DB::table('sales')
            ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('sales.seller_id', '=', $idRefCurrentUser)
            ->where('sales.isTemp', '!=', 1)
            ->select(
                'sales.id',
                'sales.customer_id',
                'sales.invoice_number',
                'sales.visit_id',
                'sales.sale_date',
                'sales.order_date',
                'sales.type',
                'sales.status',
                'sales.total',
                'sales.isTemp',
                'sales.previous_type',
                'customers.name AS customer_name',
                'customers.estate',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.next_visit_hour',
                'customer_visits.result_of_the_visit',
                'customer_visits.objective',
                'customer_visits.datetime',
            )
            ->orderBy('sales.created_at', 'DESC')
            ->get();

        $customers = DB::table('customers')
            ->where('idReference', '=', $idRefCurrentUser)
            ->where('customers.status', '=', 1)
            ->orderBy('created_at', 'DESC')
            ->get();

        $actions = [
            'Venta',
            'Presupuesto'
        ];

        return response()->json(array(
            'sales' => $sales,
            'customers' => $customers,
            'actions' => $actions,
        ));
    }

    public function store(Request $request)
    {
        /** Format Date */
        $currentDate = Carbon::now();
        $currentDate = $currentDate->toDateTimeString();

        $request->validate([
            'customer_id' => 'required',
            'type' => 'required'
        ]);

        $input = $request->all();

        /** Check is if Temporal sale or not */
        if ($input['isTemp'] == 1) {
            /** Extra temporal values */
            $input['invoice_number'] = $this->generateUniqueCode();
            $input['seller_id'] = $input['idReference'];
            $input['sale_date'] = $currentDate;

            if ($input['type'] == 'Venta') {
                $input['status'] = 'Procesado';
                $input['previous_type'] = 'Venta';
            } elseif ($input['type'] == 'Presupuesto') {
                $input['status'] = 'Pendiente';
                $input['previous_type'] = 'Presupuesto';
            }

            $input['isTemp'] = 1;
            $input['total'] = 0;

            /** Create */
            $sale = Sales::create($input);
        }

        //return response
        return response()->json(array(
            'success' => 'Sale Created successfully.',
            'data'   => $sale
        ), 200);
    }

    public function edit($id)
    {
        $customer_visit = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.visit_number', '=', $id)
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
                'customer_visits.datetime',
                'customers.name AS customer_name',
                'customers.estate',
            )
            ->orderBy('customer_visits.created_at', 'DESC')
            ->get();

        $actions = [
            'Realizar Llamada',
            'Realizar Visita',
            'Enviar Presupuesto'
        ];

        return response()->json(array(
            'customer_visit' => $customer_visit,
            'actions' => $actions,
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_id' => 'required',
            'type' => 'required'
        ]);

        $input = $request->all();

        /** Get sale by id */
        $sale = Sales::find($id);

        /** check is sale have order details items */

        if ($sale->type == 'Venta' && !$sale->visit_id) {
            /** Check If not select any product */
            $count_order = DB::table('order_details')
                ->where('order_details.sale_id', '=', $id)
                ->count();

            if ($count_order == 0) {
                return response()->json(array(
                    'errors' => 'Por favor, adicione mínimo un producto'
                ), 500);
            } else {
                /** Get total amount from items of Sale */
                $total_order = DB::table('order_details')
                    ->where('order_details.sale_id', '=', $sale->id)
                    ->sum('amount');

                /** Update Sale */
                Sales::where('sales.id', '=', $sale->id)
                    ->update([
                        'customer_id' => $input['customer_id'],
                        'isTemp' => $input['isTemp'],
                        'total' => $total_order
                    ]);

                /** send notification when is the first customer_visit order */
                if ($sale->isTemp == 1) {

                    /** Send email notification - updated status sale to process*/
                    $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();
                    $head = 'crear un(a) ' . $sale->type . ' - #' . $sale->invoice_number;
                    $type = 'Venta';
                    //** create link to download pdf invoice in email */
                    $linkOrderPDF = url('/sales/' . $input['idReference'] . '/generateInvoicePDF/?download=pdf&saleId=' . $sale->id);

                    $sale_email = DB::table('sales')
                        ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                        ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                        ->leftjoin('users', 'users.idReference', '=', 'sales.seller_id')
                        ->where('sales.id', $sale->id)
                        ->select(
                            'sales.sale_date',
                            'sales.type',
                            'sales.status',
                            'sales.total',
                            'sales.visit_id',
                            'customer_visits.action',
                            'customer_visits.visit_date',
                            'customer_visits.next_visit_date',
                            'customer_visits.next_visit_hour',
                            'customer_visits.result_of_the_visit',
                            'customer_visits.objective',
                            'customers.name AS customer_name',
                            'customers.estate',
                            'customers.phone',
                            'users.name AS seller_name'
                        )
                        ->first();

                    Mail::to($emailDefault)->send(new NotifyMail($sale_email, $head, $linkOrderPDF, $type));
                }
            }
        }

        /** if it is a order created from sales, check validations and process */
        if ($sale->type == 'Presupuesto' && !$sale->visit_id) {

            /** Check If not select any product */
            $count_order = DB::table('order_details')
                ->where('order_details.sale_id', '=', $id)
                ->count();

            if ($count_order == 0) {
                return response()->json(array(
                    'errors' => 'Por favor, adicione mínimo un producto'
                ), 500);
            } else {

                /** send notification when is the first customer_visit order */
                if ($sale->isTemp == 1) {

                    /** Send email notification - updated status sale to process*/
                    $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();
                    $head = 'crear un(a) ' . $sale->type . ' - #' . $sale->invoice_number;
                    $type = 'Venta';
                    //** create link to download pdf invoice in email */
                    $linkOrderPDF = url('/sales/' . $input['idReference'] . '/generateInvoicePDF/?download=pdf&saleId=' . $sale->id);

                    $sale_email = DB::table('sales')
                        ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                        ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                        ->leftjoin('users', 'users.idReference', '=', 'sales.seller_id')
                        ->where('sales.id', $sale->id)
                        ->select(
                            'sales.sale_date',
                            'sales.type',
                            'sales.status',
                            'sales.total',
                            'sales.visit_id',
                            'customer_visits.action',
                            'customer_visits.visit_date',
                            'customer_visits.next_visit_date',
                            'customer_visits.next_visit_hour',
                            'customer_visits.result_of_the_visit',
                            'customer_visits.objective',
                            'customers.name AS customer_name',
                            'customers.estate',
                            'customers.phone',
                            'users.name AS seller_name'
                        )
                        ->first();

                    Mail::to($emailDefault)->send(new NotifyMail($sale_email, $head, $linkOrderPDF, $type));
                }

                /** Get total amount from items of Sale */
                $total_order = DB::table('order_details')
                    ->where('order_details.sale_id', '=', $sale->id)
                    ->sum('amount');

                /** Update Sale */
                Sales::where('sales.id', '=', $sale->id)
                    ->update([
                        'customer_id' => $input['customer_id'],
                        'isTemp' => $input['isTemp'],
                        'total' => $total_order
                    ]);

                /** Update Sale with total items detail and check if process sale */
                /** Order pass to Sale */
                if ($input['orderToSale'] == true) {
                    Sales::where('sales.id', '=', $sale->id)
                        ->update([
                            'status' => 'Procesado',
                            'type' => 'Venta',
                        ]);

                    /** Send email notification - updated status sale to process*/
                    $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();
                    $head = 'procesar un ' . $sale->previous_type . ' para Venta - #' . $sale->invoice_number;
                    $type = 'Venta';

                    //** create link to download pdf invoice in email */
                    $linkOrderPDF = url('/sales/' . $input['idReference'] . '/generateInvoicePDF/?download=pdf&saleId=' . $sale->id);

                    $sale = DB::table('sales')
                        ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                        ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                        ->leftjoin('users', 'users.idReference', '=', 'sales.seller_id')
                        ->where('sales.id', $sale->id)
                        ->select(
                            'sales.sale_date',
                            'sales.type',
                            'sales.status',
                            'sales.total',
                            'sales.visit_id',
                            'customer_visits.action',
                            'customer_visits.visit_date',
                            'customer_visits.next_visit_date',
                            'customer_visits.next_visit_hour',
                            'customer_visits.result_of_the_visit',
                            'customer_visits.objective',
                            'customers.name AS customer_name',
                            'customers.estate',
                            'customers.phone',
                            'users.name AS seller_name'
                        )
                        ->first();

                    Mail::to($emailDefault)->send(new NotifyMail($sale, $head, $linkOrderPDF, $type));
                }
            }
        }

        /** if is a Order created by customer_visit, ignore validations and update the status */
        if ($sale->type == 'Presupuesto' && $sale->visit_id) {

            /** Check If not select any product */
            $count_order = DB::table('order_details')
                ->where('order_details.visit_id', '=', $sale->visit_id)
                ->count();

            if ($count_order == 0) {
                return response()->json(array(
                    'errors' => 'Por favor, adicione mínimo un producto'
                ), 500);
            } else {

                /** Here update status to process this is created by customer_visit */
                /** check if have changes in order details, get total order */
                $total_order = DB::table('order_details')
                    ->where('order_details.visit_id', '=', $sale->visit_id)
                    ->sum('amount');

                /** Update Sale with total items detail and check if process sale */
                /** Order pass to Sale */
                if ($input['orderToSale'] == true) {
                    /** update status in sales and customer_visit */
                    Sales::where('sales.id', '=', $sale->id)
                        ->update([
                            'status' => 'Procesado',
                            'type' => 'Venta',
                            'total' => $total_order
                        ]);

                    DB::table('customer_visits')
                        ->where('customer_visits.id', '=', $sale->visit_id)
                        ->where('customer_visits.seller_id', '=', $input['idReference'])
                        ->update([
                            'status' => 'Procesado',
                        ]);

                    /** Send email notification - updated status sale to process*/
                    $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();
                    $head = 'procesar un ' . $sale->previous_type . ' para Venta - #' . $sale->invoice_number;
                    $type = 'Venta';

                    //** create link to download pdf invoice in email */
                    $linkOrderPDF = url('/sales/' . $input['idReference'] . '/generateInvoicePDF/?download=pdf&saleId=' . $sale->id);

                    $sale = DB::table('sales')
                        ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                        ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                        ->leftjoin('users', 'users.idReference', '=', 'sales.seller_id')
                        ->where('sales.id', $sale->id)
                        ->select(
                            'sales.sale_date',
                            'sales.type',
                            'sales.status',
                            'sales.total',
                            'sales.visit_id',
                            'customer_visits.action',
                            'customer_visits.visit_date',
                            'customer_visits.next_visit_date',
                            'customer_visits.next_visit_hour',
                            'customer_visits.result_of_the_visit',
                            'customer_visits.objective',
                            'customers.name AS customer_name',
                            'customers.estate',
                            'customers.phone',
                            'users.name AS seller_name'
                        )
                        ->first();

                    Mail::to($emailDefault)->send(new NotifyMail($sale, $head, $linkOrderPDF, $type));
                }
            }
        }

        //return response
        return response()->json(array(
            'success' => 'Sale updated successfully.',
            'data'   => $sale
        ), 200);
    }

    public function search($textSearch, $idRefCurrentUser)
    {
        if ($textSearch == '') {
            $customers = DB::table('customers')
                ->where('idReference', '=', $idRefCurrentUser)
                ->select(
                    'id',
                    'name',
                    'last_name',
                    'category',
                    'potential_products',
                    'is_vigia',
                    'email',
                    'address',
                    'estate',
                    'phone',
                    'objective',
                    'doc_id',
                    'result_of_the_visit',
                    'next_visit_date',
                    'next_visit_hour'
                )
                ->orderBy('created_at', 'DESC')
                ->get();
        } else {
            $customers = DB::table('customers')
                ->where('idReference', '=', $idRefCurrentUser)
                ->where('name', 'LIKE', "%{$textSearch}%")
                ->select(
                    'id',
                    'name',
                    'last_name',
                    'category',
                    'potential_products',
                    'is_vigia',
                    'email',
                    'address',
                    'estate',
                    'phone',
                    'objective',
                    'doc_id',
                    'result_of_the_visit',
                    'next_visit_date',
                    'next_visit_hour'
                )
                ->orderBy('created_at', 'DESC')
                ->get();
        }

        return response()->json(array(
            'customers' => $customers
        ));
    }

    public function generateUniqueCode()
    {
        do {
            $invoice_number = random_int(100000, 999999);
        } while (
            DB::table('sales')->where("invoice_number", "=", $invoice_number)->first()
        );

        return $invoice_number;
    }

    // public function getLastID($idRefCurrentUser)
    // {
    //     $lastIdSales = DB::table('sales')
    //         ->where('sales.seller_id', '=', $idRefCurrentUser)
    //         ->max('sales.id');

    //     return response()->json(array(
    //         'lastIdSales' => $lastIdSales
    //     ));
    // }

    public function cancelSale($id, $idRefCurrentUser)
    {
        /** Get sale by id */
        $sale = Sales::find($id);

        /** check if sale/order relation with visit_customer, update status to cancel */
        if (!$sale->visit_id) {
            DB::table('sales')
                ->where('sales.id', '=', $sale->id)
                ->where('sales.seller_id', '=', $idRefCurrentUser)
                ->update([
                    'status' => 'Cancelado'
                ]);
        } else {
            DB::table('sales')
                ->where('sales.id', '=', $sale->id)
                ->where('sales.seller_id', '=', $idRefCurrentUser)
                ->update([
                    'status' => 'Cancelado'
                ]);

            DB::table('customer_visits')
                ->where('customer_visits.id', '=', $sale->visit_id)
                ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
                ->update([
                    'status' => 'Cancelado'
                ]);
        }

        /** Get the sale data for notify email */
        $sale = DB::table('sales')
            ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->leftjoin('users', 'users.idReference', '=', 'sales.seller_id')
            ->where('sales.id', $sale->id)
            ->where('sales.seller_id', '=', $idRefCurrentUser)
            ->select(
                'sales.id',
                'sales.invoice_number',
                'sales.sale_date',
                'sales.type',
                'sales.status',
                'sales.total',
                'sales.visit_id',
                'customer_visits.action',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.next_visit_hour',
                'customer_visits.result_of_the_visit',
                'customer_visits.objective',
                'customers.name AS customer_name',
                'customers.estate',
                'customers.phone',
                'users.name AS seller_name'
            )
            ->first();

        /** Send email notification - updated status sale to cancel*/
        $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();
        $head = 'Cancelar un(a) ' . $sale->type . ' - #' . $sale->invoice_number;
        $type = 'Venta';

        //** create link to download pdf invoice in email */
        $linkOrderPDF = url('/sales/' . $idRefCurrentUser . '/generateInvoicePDF/?download=pdf&saleId=' . $sale->id);

        Mail::to($emailDefault)->send(new NotifyMail($sale, $head, $linkOrderPDF, $type));

        return response()->json(array(
            'success' => 'Sale Canceled successfully.'
        ));
    }
}
