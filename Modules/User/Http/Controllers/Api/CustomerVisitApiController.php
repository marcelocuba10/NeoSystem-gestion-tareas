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

class CustomerVisitApiController extends Controller
{
    public function index($idRefCurrentUser)
    {
        $customer_visits = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
            ->where('customer_visits.isTemp', '!=', 1)
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
                'customer_visits.isTemp',
                'customer_visits.datetime',
                'customers.name AS customer_name',
                'customers.estate',
                'customers.latitude',
                'customers.longitude',
            )
            ->orderBy('customer_visits.created_at', 'DESC')
            ->get();

        $customers = DB::table('customers')
            ->select('id', 'name')
            ->where('idReference', '=', $idRefCurrentUser)
            ->where('customers.status', '=', 1)
            ->orderBy('created_at', 'DESC')
            ->get();

        $actions = [
            'Realizar Llamada',
            'Realizar Visita',
            'Enviar Presupuesto'
        ];

        return response()->json(array(
            'customer_visits' => $customer_visits,
            'customers' => $customers,
            'actions' => $actions
        ));
    }

    public function store(Request $request)
    {
        /** Format Date */
        $currentDate = Carbon::now();
        $currentDate = $currentDate->toDateTimeString();

        $request->validate([
            'customer_id' => 'nullable',
            'visit_date' => 'nullable',
            'next_visit_date' => 'nullable|max:20|min:5',
            'next_visit_hour' => 'nullable|max:20|min:5',
            'action' => 'required|max:30|min:5',
            'result_of_the_visit' => 'nullable|max:1000|min:3',
            'objective' => 'nullable|max:1000|min:3',
        ]);

        $input = $request->all();

        /** check if select 'order' is selected */
        if ($input['action'] == 'Enviar Presupuesto') {

            /** Check is if Temporal customer_visit or not */
            if ($input['isTemp'] == 1) {
                /** Extra temporal values */
                $input['visit_number'] = $this->generateUniqueCodeVisit();
                $input['seller_id'] = $input['idReference'];
                $input['customer_id'] = $input['customer_id'];
                $input['visit_date'] = $currentDate;

                $input['type'] = 'Presupuesto';
                $input['status'] = 'Pendiente';

                $input['isTemp'] = 1;

                /** Create */
                $customer_visit = CustomerVisit::create($input);

                /** Create Sale-order because order is checked */
                $sale['invoice_number'] = $customer_visit->visit_number;
                $sale['visit_id'] = $customer_visit->id;
                $sale['seller_id'] = $input['idReference'];
                $sale['customer_id'] = $customer_visit->customer_id;
                $sale['order_date'] = $customer_visit->visit_date;
                $sale['type'] = 'Presupuesto';
                $sale['previous_type'] = 'Presupuesto';
                $sale['status'] = 'Pendiente';
                $sale['isTemp'] = 1;
                $sale['total'] = 0;
                Sales::create($sale);
            }
        }

        if ($input['action'] != 'Enviar Presupuesto') {

            $input['visit_number'] = $this->generateUniqueCodeVisit();
            $input['type'] = 'Sin Presupuesto';
            $input['status'] = 'Pendiente';
            $input['visit_date'] = $currentDate;
            $input['seller_id'] = $input['idReference'];
            $customer_visit = CustomerVisit::create($input);

            /** check if next_visit_date is marked, do appointment */
            /** String comparison using strcmp(); Returns 0 if the strings are equal. */
            if (strcmp($input['next_visit_date'], 'No marcado') !== 0) {
                $field['idReference'] = $input['idReference'];
                $field['visit_number'] = $customer_visit->visit_number;
                $field['visit_id'] = $customer_visit->id;
                $field['customer_id'] = $input['customer_id'];
                $field['date'] = $input['next_visit_date'];
                $field['hour'] = $input['next_visit_hour'];
                $field['action'] =  $input['action'];
                $field['status'] = 'Pendiente';
                Appointment::create($field);
            }

            /** Send email notification */
            // $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();
            // $head = 'crear una Visita Cliente - #' . $customer_visit->visit_number;
            // $type = 'Visita Cliente';
            // $linkOrderPDF = null;

            // $customer_visit = DB::table('customer_visits')
            //     ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            //     ->leftjoin('users', 'users.idReference', '=', 'customer_visits.seller_id')
            //     ->where('customer_visits.id', $customer_visit->id)
            //     ->select(
            //         'customer_visits.id',
            //         'customer_visits.visit_number',
            //         'customer_visits.visit_date',
            //         'customer_visits.next_visit_date',
            //         'customer_visits.next_visit_hour',
            //         'customer_visits.status',
            //         'customer_visits.action',
            //         'customer_visits.type',
            //         'customer_visits.result_of_the_visit',
            //         'customer_visits.objective',
            //         'customers.name AS customer_name',
            //         'customers.estate',
            //         'customers.phone',
            //         'users.name AS seller_name'
            //     )
            //     ->first();

            // Mail::to($emailDefault)->send(new NotifyMail($customer_visit, $head, $linkOrderPDF, $type));
        }

        //return response
        return response()->json(array(
            'success' => 'Customer visit Created successfully.',
            'data'   => $customer_visit
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
        /** date validation, not less than 1980 and not greater than the current year **/
        // $initialDate = '1980-01-01';
        // $currentDate = (date('Y') + 2) . '-01-01'; //current date + 2 year

        /** Format Date */
        $currentDate = Carbon::now();
        $currentDate = $currentDate->toDateTimeString();

        $request->validate([
            'customer_id' => 'required',
            'visit_date' => 'nullable',
            'next_visit_date' => 'nullable|max:20|min:5',
            'next_visit_hour' => 'nullable|max:20|min:5',
            'action' => 'required|max:30|min:5',
            'result_of_the_visit' => 'nullable|max:1000|min:3',
            'objective' => 'nullable|max:1000|min:3',
        ]);

        $input = $request->all();

        /** Get sale by id */
        $customer_visit = CustomerVisit::find($id);

        /** check if select 'order' is selected */
        if ($input['action'] == 'Enviar Presupuesto') {

            /** If not select any product */
            $count_order = DB::table('order_details')
                ->where('order_details.visit_id', '=', $id)
                ->count();

            //** check order details */
            if ($count_order == 0) {
                return response()->json(array(
                    'errors' => 'Por favor, adicione un producto en el Presupuesto'
                ), 500);
            } else {

                /** send email notification when is the first customer_visit order */
                if ($customer_visit->isTemp == 1) {
                    /** Send email notification */
                    // $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();
                    // $head = 'crear una Visita Cliente - #' . $customer_visit->visit_number;
                    // $type = 'Visita Cliente';
                    // //** create link to download pdf invoice in email */
                    // $linkOrderPDF = url('/customer_visits/' . $input['idReference'] . '/generateInvoicePDF/?download=pdf&visit_id=' . $customer_visit->id);

                    // $customer_visit_email = DB::table('customer_visits')
                    //     ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                    //     ->leftjoin('users', 'users.idReference', '=', 'customer_visits.seller_id')
                    //     ->where('customer_visits.id', $customer_visit->id)
                    //     ->select(
                    //         'customer_visits.id',
                    //         'customer_visits.visit_number',
                    //         'customer_visits.visit_date',
                    //         'customer_visits.next_visit_date',
                    //         'customer_visits.next_visit_hour',
                    //         'customer_visits.status',
                    //         'customer_visits.action',
                    //         'customer_visits.type',
                    //         'customer_visits.result_of_the_visit',
                    //         'customer_visits.objective',
                    //         'customers.name AS customer_name',
                    //         'customers.estate',
                    //         'customers.phone',
                    //         'users.name AS seller_name'
                    //     )
                    //     ->first();

                    // Mail::to($emailDefault)->send(new NotifyMail($customer_visit_email, $head, $linkOrderPDF, $type));
                }

                /** Check if it is the first time the order is created or if it already existed */
                /** Create new sale with the values */
                if ($customer_visit->type == 'Sin Presupuesto') {
                    /** Get total amount from items of visit order */
                    $total_order = DB::table('order_details')
                        ->where('order_details.visit_id', '=', $customer_visit->id)
                        ->sum('amount');

                    //** Create Sale Order with the order visit */
                    $sale['invoice_number'] = $customer_visit->visit_number;
                    $sale['visit_id'] = $customer_visit->id;
                    $sale['seller_id'] = $input['idReference'];
                    $sale['customer_id'] = $customer_visit->customer_id;
                    $sale['order_date'] = $customer_visit->visit_date;
                    $sale['type'] = 'Presupuesto';
                    $sale['previous_type'] = 'Presupuesto';
                    $sale['status'] = 'Pendiente';
                    $sale['total'] = $total_order;
                    $sale['isTemp'] = $input['isTemp'];
                    Sales::create($sale);
                } else {
                    /** update total in sales, already exist order sale */
                    $total_order = DB::table('order_details')
                        ->where('order_details.visit_id', '=', $customer_visit->id)
                        ->sum('amount');

                    Sales::where('sales.visit_id', '=', $customer_visit->id)
                        ->update([
                            'customer_id' => $input['customer_id'],
                            'isTemp' => $input['isTemp'],
                            'total' => $total_order
                        ]);
                }

                /** check if next_visit_date is marked, create or update appointment */
                /** String comparison using strcmp(); Returns 0 if the strings are equal. */
                if (strcmp($input['next_visit_date'], 'No marcado') !== 0) {
                    $appointment = DB::table('appointments')
                        ->where('appointments.visit_id', '=', $customer_visit->id)
                        ->first();

                    /** if appointment exist, update, else create new appointment */
                    if ($appointment) {
                        DB::table('appointments')
                            ->where('appointments.visit_id', '=', $customer_visit->id)
                            ->where('appointments.idReference', '=', $input['idReference'])
                            ->update([
                                'idReference' => $input['idReference'],
                                'visit_id' => $customer_visit->id,
                                'customer_id' => $input['customer_id'],
                                'date' => $input['next_visit_date'],
                                'hour' => $input['next_visit_hour'],
                                'action' => $input['action'],
                                'status' => 'Pendiente'
                            ]);
                    } else {
                        $field['idReference'] = $input['idReference'];
                        $field['visit_number'] = $customer_visit->visit_number;
                        $field['visit_id'] = $customer_visit->id;
                        $field['customer_id'] = $input['customer_id'];
                        $field['date'] = $input['next_visit_date'];
                        $field['hour'] = $input['next_visit_hour'];
                        $field['action'] =  $input['action'];
                        $field['status'] = 'Pendiente';
                        Appointment::create($field);
                    }
                }

                /** Set extra values */
                $input['type'] = 'Presupuesto';

                /** Update */
                $customer_visit->update($input);
            }
        }

        if ($input['action'] != 'Enviar Presupuesto') {

            /** Check if have order details, but action is different to Order */
            $count_order = DB::table('order_details')
                ->where('order_details.visit_id', '=', $id)
                ->count();

            if ($count_order != 0) {
                /** remove item order */
                DB::table('order_details')
                    ->where('visit_id', $id)
                    ->delete();

                /** remove sale with visit_id */
                DB::table('sales')
                    ->where('visit_id', $id)
                    ->delete();

                /** add extra items in customer_visit and UPDATE */
                $input['visit_date'] = Carbon::now();
                $input['type'] = 'Sin Presupuesto';
                $customer_visit->update($input);
            } else {
                /** add extra items in customer visit and UPDATE */
                $input['visit_date'] = Carbon::now();
                $input['type'] = 'Sin Presupuesto';
                $customer_visit->update($input);
            }

            /** check if next_visit_date is marked, create or update appointment */
            /** String comparison using strcmp(); Returns 0 if the strings are equal. */
            if (strcmp($input['next_visit_date'], 'No marcado') !== 0) {
                $appointment = DB::table('appointments')
                    ->where('appointments.visit_id', '=', $customer_visit->id)
                    ->first();

                /** if appointment exist, update, else create new appointment */
                if ($appointment) {
                    DB::table('appointments')
                        ->where('appointments.visit_id', '=', $customer_visit->id)
                        ->where('appointments.idReference', '=', $input['idReference'])
                        ->update([
                            'idReference' => $input['idReference'],
                            'visit_id' => $customer_visit->id,
                            'customer_id' => $input['customer_id'],
                            'date' => $input['next_visit_date'],
                            'hour' => $input['next_visit_hour'],
                            'action' => $input['action'],
                            'status' => 'Pendiente'
                        ]);
                } else {
                    $field['idReference'] = $input['idReference'];
                    $field['visit_number'] = $customer_visit->visit_number;
                    $field['visit_id'] = $customer_visit->id;
                    $field['customer_id'] = $input['customer_id'];
                    $field['date'] = $input['next_visit_date'];
                    $field['hour'] = $input['next_visit_hour'];
                    $field['action'] =  $input['action'];
                    $field['status'] = 'Pendiente';
                    Appointment::create($field);
                }
            }
        }

        //return response
        return response()->json(array(
            'success' => 'Customer visit updated successfully.',
            'data'   => $customer_visit
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

    public function generateUniqueCodeVisit()
    {
        do {
            $visit_number = random_int(100000, 999999);
        } while (
            DB::table('customer_visits')->where("visit_number", "=", $visit_number)->first()
        );

        return $visit_number;
    }

    public function generateUniqueCodeSale()
    {
        do {
            $invoice_number = random_int(100000, 999999);
        } while (
            DB::table('sales')->where("invoice_number", "=", $invoice_number)->first()
        );

        return $invoice_number;
    }

    public function getLastID($idRefCurrentUser)
    {
        $lastIdCustomer_visits = DB::table('customer_visits')
            ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
            ->select('customer_visits.id')
            ->orderBy('customer_visits.created_at', 'DESC')
            ->limit(1)
            ->get();

        return response()->json(array(
            'lastIdCustomer_visits' => $lastIdCustomer_visits
        ));
    }

    public function cancelCustomerVisit($id, $idRefCurrentUser)
    {
        /** Proceed to cancel the visit */
        DB::table('customer_visits')
            ->where('customer_visits.id', '=', $id)
            ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
            ->update([
                'status' => 'Cancelado',
            ]);

        /** Get the visit data for notify email */
        $customer_visit = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->leftjoin('users', 'users.idReference', '=', 'customer_visits.seller_id')
            ->where('customer_visits.id', $id)
            ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
            ->select(
                'customer_visits.id',
                'customer_visits.visit_number',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.next_visit_hour',
                'customer_visits.status',
                'customer_visits.action',
                'customer_visits.type',
                'customer_visits.result_of_the_visit',
                'customer_visits.objective',
                'customers.name AS customer_name',
                'customers.estate',
                'customers.phone',
                'users.name AS seller_name'
            )
            ->first();

        /** is have next_visit_date, need update status in the appointment*/
        if ($customer_visit->next_visit_date != 'No marcado') {
            DB::table('appointments')
                ->where('appointments.visit_id', '=', $id)
                ->where('appointments.idReference', '=', $idRefCurrentUser)
                ->update([
                    'status' => 'Cancelado', //appointment status canceled
                ]);
        }

        /** is customer_visit have order, need update status in the sale*/
        if ($customer_visit->action == 'Enviar Presupuesto') {
            DB::table('sales')
                ->where('sales.visit_id', '=', $id)
                ->where('sales.seller_id', '=', $idRefCurrentUser)
                ->update([
                    'status' => 'Cancelado', //sale status canceled
                ]);
        }

        /** Send email notification - updated status visit to cancel*/
        // $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();
        // $head = 'Cancelar una Visita Cliente - #' . $customer_visit->visit_number;
        // $type = 'Visita Cliente';

        // //** create link to download pdf invoice in email */
        // if ($customer_visit->type == 'Presupuesto') {
        //     $linkOrderPDF = url('/customer_visits/' . $idRefCurrentUser . '/generateInvoicePDF/?download=pdf&visit_id=' . $customer_visit->id);
        // } else {
        //     $linkOrderPDF = null;
        // }

        // Mail::to($emailDefault)->send(new NotifyMail($customer_visit, $head, $linkOrderPDF, $type));

        return response()->json(array(
            'success' => 'Customer Visit canceled successfully.'
        ));
    }

    public function pendingToProcess(Request $request, $id)
    {
        /** date validation, not less than 1980 and not greater than the current year **/
        $initialDate = '1980-01-01';
        $currentDate = (date('Y') + 2) . '-01-01'; //current date + 2 year

        $request->validate([
            'customer_id' => 'required',
            'visit_date' => 'nullable',
            'next_visit_date' => 'nullable|max:15|min:5',
            'next_visit_hour' => 'nullable|max:15|min:5',
            'action' => 'required|max:30|min:5',
            'result_of_the_visit' => 'nullable|max:1000|min:3',
            'objective' => 'nullable|max:1000|min:3',
        ]);

        $input = $request->all();

        /** Get customer_visit by id */
        $customer_visit = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->leftjoin('users', 'users.idReference', '=', 'customer_visits.seller_id')
            ->where('customer_visits.id', $id)
            ->select(
                'customer_visits.id',
                'customer_visits.visit_number',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.next_visit_hour',
                'customer_visits.status',
                'customer_visits.action',
                'customer_visits.type',
                'customer_visits.result_of_the_visit',
                'customer_visits.objective',
                'customers.name AS customer_name',
                'customers.estate',
                'customers.phone',
                'users.name AS seller_name'
            )
            ->first();

        //** Update status in customer visit */
        DB::table('customer_visits')
            ->where('id', '=', $id)
            ->update([
                'status' => 'Procesado',
            ]);

        /** check if next_visit_date is marked, change status in appointment */
        if ($input['next_visit_date'] != 'No marcado') {
            DB::table('appointments')
                ->where('appointments.visit_id', '=', $id)
                ->where('appointments.idReference', '=', $input['idReference'])
                ->update([
                    'status' => 'Procesado'
                ]);
        }

        /** Send email notification - updated status visit to proceseed*/
        // $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();
        // $head = 'procesar una Visita Cliente - #' . $customer_visit->visit_number;
        // $type = 'Visita Cliente';

        // //** create link to download pdf invoice in email */
        // if ($customer_visit->type == 'Presupuesto') {
        //     $linkOrderPDF = url('/customer_visits/' . $input['idReference'] . '/generateInvoicePDF/?download=pdf&visit_id=' . $customer_visit->id);
        // } else {
        //     $linkOrderPDF = null;
        // }

        // Mail::to($emailDefault)->send(new NotifyMail($customer_visit, $head, $linkOrderPDF, $type));

        //return response
        return response()->json(array(
            'success' => 'Customer visit processed successfully.',
            'data'   => $customer_visit
        ), 200);
    }
}
