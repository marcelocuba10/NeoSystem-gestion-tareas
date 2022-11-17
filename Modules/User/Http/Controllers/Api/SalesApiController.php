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
            ->orWhere('customer_visits.seller_id', '=', $idRefCurrentUser)
            ->select(
                'sales.id',
                'sales.customer_id',
                'sales.invoice_number',
                'sales.sale_date',
                'sales.order_date',
                'sales.type',
                'sales.status',
                'sales.total',
                'customers.name AS customer_name',
                'customers.estate',
                'customer_visits.visit_date',
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

        /** Check If not select any product */
        $count_order = DB::table('order_details')
            ->where('order_details.visit_id', '=', $input['id'])
            ->count();

        if ($count_order == 0) {
            return response()->json(array(
                'errors' => 'Por favor, adicione mínimo un producto'
            ), 500);
        } else {

            /** Extra values */
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

            /** Get total amount from items orders of Sale */
            $total_order = DB::table('order_details')
                ->where('order_details.sale_id', '=', $input['id'])
                ->sum('amount');

            $input['total'] = $total_order;

            /** Create */
            $sale = Sales::create($input);

            /** Send email notification - updated status sale to process*/
            $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();
            $head = 'crear un(a) ' . $sale->type . ' - #' . $sale->invoice_number;
            $type = 'Venta';
            $linkOrderPDF = url('/user/sales/generateInvoicePDF/?download=pdf&saleId=' . $sale->id);

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

        // if ($input['next_visit_date'] != null && $input['objective'] == null) {
        //     return response()->json(array(
        //         'errors' => 'Por favor, agregue los Objetivos para la próxima visita marcada'
        //     ), 500);
        // }

        // if ($input['next_visit_date'] != null && $input['next_visit_hour'] == null) {
        //     return response()->json(array(
        //         'errors' => 'Por favor, agregue la Hora de la próxima visita'
        //     ), 500);
        // }

        // if ($input['next_visit_date'] == null || $input['next_visit_hour'] == null) {
        //     $input['next_visit_date'] = 'No marcado';
        //     $input['next_visit_hour'] = 'No marcado';
        //     $input['objective'] = null;
        // }

        /** check if select 'order' is selected */
        if ($input['action'] == 'Enviar Presupuesto') {
            /** If not select any product */
            $count_order = DB::table('order_details')
                ->where('order_details.visit_id', '=', $id)
                ->count();

            if ($count_order == 0) {
                return response()->json(array(
                    'errors' => 'Por favor, adicione un producto en el Presupuesto'
                ), 500);
            } else {

                $customer_visit = CustomerVisit::find($id);

                /** Get total amount from items of visit order to Sale */
                $total_order = DB::table('order_details')
                    ->where('order_details.visit_id', '=', $customer_visit->id)
                    ->sum('amount');

                $sale['invoice_number'] = $customer_visit->visit_number;
                $sale['visit_id'] = $customer_visit->id;
                $sale['seller_id'] = $input['idReference'];
                $sale['customer_id'] = $customer_visit->customer_id;
                $sale['order_date'] = $customer_visit->visit_date;
                $sale['type'] = 'Presupuesto';
                $sale['previous_type'] = 'Presupuesto';
                $sale['status'] = 'Pendiente';
                $sale['total'] = $total_order;
                Sales::create($sale);

                /** add extra items in customer visit and UPDATE */
                $input['type'] = 'Presupuesto';
                $input['visit_date'] = Carbon::now();
                $input['seller_id'] = $input['idReference'];
                $customer_visit->update($input);

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
        } elseif ($input['action'] != 'Enviar Presupuesto') {

            /** Check if have order details, but action is different to Order */
            $count_order = DB::table('order_details')
                ->where('order_details.visit_id', '=', $id)
                ->count();

            if ($count_order != 0) {
                /** remove item order */
                DB::table('order_details')
                    ->where('visit_id', $id)
                    ->delete();
            }

            /** add extra items in customer visit and UPDATE */
            $input['visit_date'] = Carbon::now();
            $customer_visit = CustomerVisit::find($id);
            $customer_visit->update($input);

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

    public function generateUniqueCode()
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
        $lastIdSales = DB::table('sales')
            ->where('sales.seller_id', '=', $idRefCurrentUser)
            ->select('sales.id')
            ->orderBy('sales.created_at', 'DESC')
            ->limit(1)
            ->get();

        return response()->json(array(
            'lastIdSales' => $lastIdSales
        ));
    }

    public function destroy($id)
    {
        Customers::find($id)->delete();
        return redirect()->to('/user/customers')->with('message', 'Customer deleted successfully');
    }
}
