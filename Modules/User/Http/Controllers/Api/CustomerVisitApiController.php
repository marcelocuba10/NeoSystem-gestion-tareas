<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\CustomerParameters;
use Modules\User\Entities\Customers;
use Modules\User\Entities\CustomerVisit;

class CustomerVisitApiController extends Controller
{

    public function index($idRefCurrentUser)
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
            )
            ->orderBy('customer_visits.created_at', 'DESC')
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

        return response()->json(array(
            'customer_visits' => $customer_visits,
            'customers' => $customers,
            'actions' => $actions,
        ));
    }

    public function store(Request $request)
    {
        /** date validation, not less than 1980 and not greater than the current year **/
        $initialDate = '1980-01-01';
        $currentDate = (date('Y') + 1) . '-01-01'; //2023-01-01

        $request->validate([
            'name' => 'required|max:50|min:5',
            'phone' => 'nullable|max:25|min:5',
            'doc_id' => 'nullable|max:25|min:5|unique:customers,doc_id',
            'email' => 'nullable|max:50|min:5|email:rfc,dns|unique:customers,email',
            'address' => 'nullable|max:255|min:5',
            'city' => 'nullable|max:50|min:5',
            'estate' => 'required|max:50|min:5',
            'is_vigia' => 'nullable',
            'category' => 'required|max:150|min:1',
            'potential_products' => 'required|max:150|min:1',
            'result_of_the_visit' => 'nullable|max:1000|min:3',
            'objective' => 'nullable|max:1000|min:3',
            'next_visit_date' => 'nullable|date_format:Y-m-d|after_or_equal:' . $initialDate . '|before:' . $currentDate,
            'next_visit_hour' => 'nullable|max:10|min:5',
        ]);

        $input = $request->all();

        /** create temporal Customer */
        $customer = Customers::create($input);

        /** potential products array */
        foreach ($request->potential_products as $key => $value) {

            /** Save potential product in table customer_parameters */
            $item = new CustomerParameters();
            $item->customer_id = $customer->id;
            $item->potential_product_id = $request->potential_products[$key];
            $item->quantity = 1;
            $item->save();
        }

        /** categories array */
        foreach ($request->category as $key => $value) {
            /** Save items in table customer_parameters */
            $item = new CustomerParameters();
            $item->customer_id = $customer->id;
            $item->category_id = $request->category[$key];
            $item->save();
        }

        //return response
        return response()->json(array(
            'success' => 'Customer created successfully.',
            'data'   => $customer
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
            'next_visit_date' => 'nullable|date|after_or_equal:today|before:' . $currentDate,
            'next_visit_hour' => 'nullable|max:5|min:5',
            'action' => 'required|max:30|min:5',
            'result_of_the_visit' => 'required|max:1000|min:3',
            'objective' => 'nullable|max:1000|min:3',
            'product_id' => 'nullable',
            'qty' => 'nullable|min:0',
            'price' => 'nullable',
            'amount' => 'nullable',
        ]);

        $input = $request->all();

        if ($input['next_visit_date'] != null && $input['objective'] == null) {
            return back()->with('error', 'Por favor, agregue los Objetivos para la próxima visita marcada');
        }

        if ($input['next_visit_date'] != null && $input['next_visit_hour'] == null) {
            return back()->with('error', 'Por favor, agregue la Hora de la próxima visita');
        }

        if ($input['next_visit_date'] == null || $input['next_visit_hour'] == null) {
            $input['next_visit_date'] = 'No marcado';
            $input['next_visit_hour'] = 'No marcado';
            $input['objective'] = null;
        }

        /** check if select 'order' is selected */
        if ($request->isSetOrder == 'true') {
            /** If not select any product */
            if (strlen($request->product_id[0]) > 10) {
                return back()->with('error', 'Por favor, adicione un producto.');
            } else {

                $customer_visit = CustomerVisit::find($id);

                if ($customer_visit->type == 'Sin Presupuesto') {

                    foreach ($request->product_id as $key => $value) {
                        if (intval($request->qty[$key]) <= 0) {
                            return back()->with('error', 'Por favor, ingrese una cantidad válida.');
                        } else {

                            /** Save order details of customer visit */
                            $order = new OrderDetail();
                            $order->product_id = $request->product_id[$key];
                            $order->visit_id = $customer_visit->id;
                            $order->quantity = $request->qty[$key];
                            $order->price = str_replace(',', '', $request->price[$key]);
                            $order->amount = $request->amount[$key];
                            $order->save();
                        }
                    }

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
                } else {

                    /** Get old items saved in order details in array*/
                    $order_details = DB::table('order_details')
                        ->where('order_details.visit_id', '=', $id)
                        ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
                        ->pluck('product_id')
                        ->toArray();

                    /** Check if in my new array items contain new id_product */
                    $differenceArray = array_diff($request->product_id, $order_details);

                    if (count($differenceArray) > 0) {

                        /** Array have news product_id, add news items order detail*/
                        foreach ($differenceArray as $key => $value) {
                            if (intval($request->qty[$key]) <= 0) {
                                return back()->with('error', 'Por favor, ingrese una cantidad válida.');
                            } else {
                                /** Save order details of customer visit */
                                $order = new OrderDetail();
                                $order->product_id = $request->product_id[$key];
                                $order->visit_id = $customer_visit->id;
                                $order->quantity = $request->qty[$key];
                                $order->price = str_replace(',', '', $request->price[$key]);
                                $order->amount = $request->amount[$key];
                                $order->save();
                            }
                        }
                    } else {
                        /** Array not have news product_id, update values */
                        foreach ($request->product_id as $key => $value) {
                            if (intval($request->qty[$key]) <= 0) {
                                return back()->with('error', 'Por favor, ingrese una cantidad válida.');
                            } else {

                                /** if find product id, if exist product_id, update values else delete old product_id and create new item detail with the new product_id */
                                $order_detail_id = DB::table('order_details')
                                    ->where('order_details.product_id', '=', $request->product_id[$key])
                                    ->where('order_details.visit_id', '=', $customer_visit->id)
                                    ->select(
                                        'order_details.product_id',
                                    )
                                    ->first();

                                if ($order_detail_id) {
                                    DB::table('order_details')
                                        ->where('order_details.visit_id', '=', $customer_visit->id)
                                        ->where('order_details.product_id', '=', $request->product_id[$key])
                                        ->update([
                                            'quantity' => $request->qty[$key],
                                            'amount' => $request->amount[$key]
                                        ]);
                                } else {

                                    /** Add new item detail, with the new product_id in order detail */
                                    $order = new OrderDetail();
                                    $order->product_id = $request->product_id[$key];
                                    $order->visit_id = $customer_visit->id;
                                    $order->quantity = $request->qty[$key];
                                    $order->price = str_replace(',', '', $request->price[$key]);
                                    $order->amount = $request->amount[$key];
                                    $order->save();

                                    // /** delete old item product_id in order detail*/
                                    // foreach ($request->product_id as $key => $value) {
                                    //     DB::table('order_details')
                                    //         ->where('product_id', $request->id)
                                    //         ->where('visit_id', $customer_visit->id)
                                    //         ->delete();
                                    // }
                                }
                            }
                        }
                    }

                    $total_order = DB::table('order_details')
                        ->where('order_details.visit_id', '=', $customer_visit->id)
                        ->sum('amount');

                    Sales::where('sales.visit_id', '=', $customer_visit->id)
                        ->update([
                            'total' => $total_order
                        ]);
                }

                /** add extra items in customer visit */
                $input['type'] = 'Presupuesto';
                $input['visit_date'] = Carbon::now();
                $input['seller_id'] = $input['idReference'];
                $customer_visit->update($input);
            }
        } elseif ($request->isSetOrder == 'false') {
            $input['visit_date'] = Carbon::now();
            $customer_visit = CustomerVisit::find($id);
            $customer_visit->update($input);

            /** check if next_visit_date is marked, create or update appointment */
            if ($input['next_visit_date'] != 'No marcado') {

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
            'success' => 'Customer updated successfully.',
            'data'   => $customer_visit
        ));
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

    public function destroy($id)
    {
        Customers::find($id)->delete();
        return redirect()->to('/user/customers')->with('message', 'Customer deleted successfully');
    }
}
