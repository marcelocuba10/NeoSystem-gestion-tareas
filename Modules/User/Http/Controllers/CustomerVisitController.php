<?php

namespace Modules\User\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\Appointment;
use Modules\User\Entities\CustomerVisit;
use Modules\User\Entities\OrderDetail;
use Modules\User\Entities\Sales;

use PDF;
use Mail;
use Modules\User\Emails\NotifyMail;

class CustomerVisitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web', ['except' => ['logout']]);

        $this->middleware('permission:customer_visit-list|customer_visit-create|customer_visit-edit|customer_visit-delete', ['only' => ['index']]);
        $this->middleware('permission:customer_visit-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:customer_visit-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:customer_visit-delete', ['only' => ['destroy']]);
    }

    public function index()
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
                'customer_visits.status',
                'customer_visits.type',
                'customer_visits.action',
                'customers.name AS customer_name',
                'customers.estate'
            )
            ->orderBy('customer_visits.created_at', 'DESC')
            ->paginate(20);

        return view('user::customer_visits.index', compact('customer_visits'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function create()
    {
        $customer_visit = null;
        $currentDate = Carbon::now()->format('d/m/y H:i');

        $idRefCurrentUser = Auth::user()->idReference;

        $customers = DB::table('customers')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->where('customers.status', '=', 1)
            ->select('customers.id', 'customers.name')
            ->get();

        $actions = [
            'Realizar Llamada',
            'Realizar Visita',
            'Enviar Presupuesto'
        ];

        $products = DB::table('products')
            ->select(
                'id',
                'name',
                'sale_price',
            )
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('user::customer_visits.create', compact('customers', 'customer_visit', 'currentDate', 'actions', 'products'));
    }

    public function store(Request $request)
    {
        $idRefCurrentUser = Auth::user()->idReference;

        /** date validation, not less than 1980 and not greater than the current year **/
        $initialDate = '1980-01-01';
        $currentDate = (date('Y') + 2) . '-01-01'; //current date + 2 year

        $request->validate([
            'customer_id' => 'required',
            'visit_date' => 'required',
            'next_visit_date' => 'nullable|date|after_or_equal:today|before:' . $currentDate,
            'next_visit_hour' => 'nullable|max:5|min:5',
            'action' => 'required|max:30|min:5',
            'result_of_the_visit' => 'nullable|max:1000|min:3',
            'objective' => 'nullable|max:1000|min:3',
            'product_id' => 'required',
            'qty' => 'required',
            'price' => 'required',
            'amount' => 'required',
        ]);

        $input = $request->all();

        //validations
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
                $input['type'] = 'Presupuesto';
                $input['status'] = 'Pendiente';
                $input['visit_date'] = Carbon::now();
                $input['visit_number'] = $this->generateUniqueCodeVisit();
                $input['seller_id'] = $idRefCurrentUser;
                $customer_visit = CustomerVisit::create($input);

                foreach ($request->product_id as $key => $value) {
                    if (intval($request->qty[$key]) <= 0) {
                        $CustomerVisitIsDeleted = CustomerVisit::find($customer_visit->id)->delete();
                        return back()->with('error', 'Por favor, ingrese una cantidad válida.');
                    } else {
                        /** Save order detail of customer visit */
                        $order = new OrderDetail();
                        $order->product_id = $request->product_id[$key];
                        $order->visit_id = $customer_visit->id;
                        $order->quantity = $request->qty[$key];
                        $order->price = str_replace(',', '', $request->price[$key]);
                        $order->amount = $request->amount[$key];
                        $order->save();

                        /** if there is an error in order detail, CustomerVisitIsDeleted is true, else false*/
                        $CustomerVisitIsDeleted = false;
                    }
                }

                /** If everything is ok, proceed with the operation */
                if ($CustomerVisitIsDeleted == false) {
                    /** Get total amount from items of visit order */
                    $total_order = DB::table('order_details')
                        ->where('order_details.visit_id', '=', $customer_visit->id)
                        ->sum('amount');

                    /** Create Sale-order because order is checked */
                    $sale['invoice_number'] = $customer_visit->visit_number;
                    $sale['visit_id'] = $customer_visit->id;
                    $sale['seller_id'] = $idRefCurrentUser;
                    $sale['customer_id'] = $customer_visit->customer_id;
                    $sale['order_date'] = $customer_visit->visit_date;
                    $sale['type'] = 'Presupuesto';
                    $sale['previous_type'] = 'Presupuesto';
                    $sale['status'] = 'Pendiente';
                    $sale['total'] = $total_order;
                    Sales::create($sale);

                    /** check if next_visit_date is marked, do appointment */
                    if ($input['next_visit_date'] != 'No marcado') {
                        $field['idReference'] = $idRefCurrentUser;
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
                    $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();
                    $head = 'crear una Visita Cliente - #' . $customer_visit->visit_number;
                    $type = 'Visita Cliente';
                    $linkOrderPDF = url('/user/customer_visits/generateInvoicePDF/?download=pdf&visit_id=' . $customer_visit->id);

                    $customer_visit = DB::table('customer_visits')
                        ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                        ->leftjoin('users', 'users.idReference', '=', 'customer_visits.seller_id')
                        ->where('customer_visits.id', $customer_visit->id)
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

                    Mail::to($emailDefault)->send(new NotifyMail($customer_visit, $head, $linkOrderPDF, $type));
                }
            }
        } elseif ($request->isSetOrder == 'false') {

            $input['visit_number'] = $this->generateUniqueCodeVisit();
            $input['type'] = 'Sin Presupuesto';
            $input['status'] = 'Pendiente';
            $input['visit_date'] = Carbon::now();
            $input['seller_id'] = $idRefCurrentUser;
            $customer_visit = CustomerVisit::create($input);

            /** check if next_visit_date is marked, do appointment */
            if ($input['next_visit_date'] != 'No marcado') {
                $field['idReference'] = $idRefCurrentUser;
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
            $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();
            $head = 'crear una Visita Cliente - #' . $customer_visit->visit_number;
            $type = 'Visita Cliente';
            $linkOrderPDF = null;

            $customer_visit = DB::table('customer_visits')
                ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                ->leftjoin('users', 'users.idReference', '=', 'customer_visits.seller_id')
                ->where('customer_visits.id', $customer_visit->id)
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

            Mail::to($emailDefault)->send(new NotifyMail($customer_visit, $head, $linkOrderPDF, $type));
        }

        return redirect()->to('/user/customer_visits')->with('message', 'Visita Cliente Creada Correctamente');
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

    public function show($id)
    {
        $customer_visit = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.id', '=', $id)
            ->select(
                'customer_visits.id',
                'customer_visits.visit_number',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.next_visit_hour',
                'customer_visits.result_of_the_visit',
                'customer_visits.objective',
                'customer_visits.action',
                'customer_visits.type',
                'customer_visits.status',
                'customers.name AS customer_name',
                'customers.estate'
            )
            ->orderBy('customer_visits.created_at', 'DESC')
            ->first();

        // dd($customer_visit);

        $order_details = DB::table('order_details')
            ->where('order_details.visit_id', '=', $id)
            ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
            ->select(
                'products.name',
                'products.custom_code',
                'order_details.price',
                'order_details.quantity',
                'order_details.inventory',
                'order_details.amount'
            )
            ->orderBy('order_details.created_at', 'DESC')
            ->get();

        $total_order = DB::table('order_details')
            ->where('order_details.visit_id', '=', $id)
            ->sum('amount');

        return view('user::customer_visits.show', compact('customer_visit', 'order_details', 'total_order'));
    }

    public function edit($id)
    {
        $idRefCurrentUser = Auth::user()->idReference;
        $currentDate = Carbon::now();
        $currentDate = $currentDate->toDateTimeString();

        $customer_visit = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.id', '=', $id)
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
            ->first();

        $products = DB::table('products')
            ->select(
                'id',
                'name',
                'sale_price',
            )
            ->orderBy('created_at', 'DESC')
            ->get();

        $customers = DB::table('customers')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->where('customers.status', '=', 1)
            ->select('customers.id', 'customers.name')
            ->get();

        $actions = [
            'Realizar Llamada',
            'Realizar Visita',
            'Enviar Presupuesto'
        ];

        $order_details = DB::table('order_details')
            ->where('order_details.visit_id', '=', $id)
            ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
            ->select('products.name', 'products.code', 'order_details.price', 'order_details.quantity', 'order_details.inventory', 'order_details.amount', 'order_details.product_id')
            ->orderBy('order_details.created_at', 'DESC')
            ->get();

        $total_order = DB::table('order_details')
            ->where('order_details.visit_id', '=', $id)
            ->sum('amount');

        return view('user::customer_visits.edit', compact('customers', 'customer_visit', 'currentDate', 'actions', 'products', 'order_details', 'total_order'));
    }

    public function update(Request $request, $id)
    {
        $idRefCurrentUser = Auth::user()->idReference;

        /** date validation, not less than 1980 and not greater than the current year **/
        $initialDate = '1980-01-01';
        $currentDate = (date('Y') + 2) . '-01-01'; //current date + 2 year

        $request->validate([
            'customer_id' => 'required',
            'visit_date' => 'required',
            'next_visit_date' => 'nullable|date|after_or_equal:today|before:' . $currentDate,
            'next_visit_hour' => 'nullable|max:5|min:5',
            'action' => 'required|max:30|min:5',
            'result_of_the_visit' => 'required|max:1000|min:3',
            'objective' => 'nullable|max:1000|min:3',
            'product_id' => 'required',
            'qty' => 'required|min:0',
            'price' => 'required',
            'amount' => 'required',
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
                    $sale['seller_id'] = $idRefCurrentUser;
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

                /** add extra items in customer visit and UPDATE*/
                $input['type'] = 'Presupuesto';
                $input['visit_date'] = Carbon::now();
                $input['seller_id'] = $idRefCurrentUser;
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
                            ->where('appointments.idReference', '=', $idRefCurrentUser)
                            ->update([
                                'idReference' => $idRefCurrentUser,
                                'visit_id' => $customer_visit->id,
                                'customer_id' => $input['customer_id'],
                                'date' => $input['next_visit_date'],
                                'hour' => $input['next_visit_hour'],
                                'action' => $input['action'],
                                'status' => 'Pendiente'
                            ]);
                    } else {
                        $field['idReference'] = $idRefCurrentUser;
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
        } elseif ($request->isSetOrder == 'false') {
            /** add extra items in customer visit and UPDATE*/
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
                        ->where('appointments.idReference', '=', $idRefCurrentUser)
                        ->update([
                            'idReference' => $idRefCurrentUser,
                            'visit_id' => $customer_visit->id,
                            'customer_id' => $input['customer_id'],
                            'date' => $input['next_visit_date'],
                            'hour' => $input['next_visit_hour'],
                            'action' => $input['action'],
                            'status' => 'Pendiente'
                        ]);
                } else {
                    $field['idReference'] = $idRefCurrentUser;
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

        /** Check if button pending to process is checked, change status to processs in customer visits and appointments */
        if ($request->pendingToProcess == true) {
            DB::table('customer_visits')
                ->where('id', '=', $id)
                ->update([
                    'status' => 'Procesado',
                ]);

            DB::table('appointments')
                ->where('appointments.visit_id', '=', $id)
                ->where('appointments.idReference', '=', $idRefCurrentUser)
                ->update([
                    'status' => 'Procesado'
                ]);

            /** Send email notification - updated status visit to proceseed*/
            $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();
            $head = 'procesar una Visita Cliente - #' . $customer_visit->visit_number;
            $type = 'Visita Cliente';

            if ($customer_visit->type == 'Presupuesto') {
                $linkOrderPDF = url('/user/customer_visits/generateInvoicePDF/?download=pdf&visit_id=' . $customer_visit->id);
            } else {
                $linkOrderPDF = null;
            }

            $customer_visit = DB::table('customer_visits')
                ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                ->leftjoin('users', 'users.idReference', '=', 'customer_visits.seller_id')
                ->where('customer_visits.id', $customer_visit->id)
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

            Mail::to($emailDefault)->send(new NotifyMail($customer_visit, $head, $linkOrderPDF, $type));

            return redirect()->to('/user/customer_visits')->with('message', 'Visita Cliente actualizada correctamente.');
        } else {
            return back()->with('message', 'Visita Cliente actualizada correctamente.');
        }
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
                    'customer_visits.visit_number',
                    'customer_visits.visit_date',
                    'customer_visits.next_visit_date',
                    'customer_visits.next_visit_hour',
                    'customer_visits.result_of_the_visit',
                    'customer_visits.objective',
                    'customer_visits.status',
                    'customer_visits.type',
                    'customer_visits.action',
                    'customers.name AS customer_name',
                    'customers.estate'
                )
                ->orderBy('customer_visits.created_at', 'DESC')
                ->paginate(20);
        } else {
            $customer_visits = DB::table('customer_visits')
                ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                ->where('customers.name', 'LIKE', "%{$search}%")
                ->orWhere('customer_visits.visit_number', 'LIKE', "%{$search}%")
                ->where('customers.idReference', '=', $idRefCurrentUser)
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
                    'customer_visits.action',
                    'customers.name AS customer_name',
                    'customers.estate'
                )
                ->orderBy('customer_visits.created_at', 'DESC')
                ->paginate();
        }

        return view('user::customer_visits.index', compact('customer_visits', 'search'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function generateInvoicePDF(Request $request)
    {
        $idRefCurrentUser = Auth::user()->idReference;

        $customer_visit = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.id', '=', $request->visit_id)
            ->select(
                'customer_visits.id',
                'customer_visits.visit_number',
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
                'customers.doc_id',
                'customers.email',
                'customers.phone',
                'customers.address'
            )
            ->orderBy('customer_visits.created_at', 'DESC')
            ->first();

        $user = DB::table('users')
            ->where('users.idReference', '=', $idRefCurrentUser)
            ->select('name', 'phone_1', 'doc_id', 'address', 'email', 'city', 'estate')
            ->first();

        $order_details = DB::table('order_details')
            ->where('order_details.visit_id', '=', $request->visit_id)
            ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
            ->select(
                'products.name',
                'products.custom_code',
                'order_details.price',
                'order_details.quantity',
                'order_details.inventory',
                'order_details.amount'
            )
            ->orderBy('order_details.created_at', 'DESC')
            ->get();

        $total_order = DB::table('order_details')
            ->where('order_details.visit_id', '=', $request->visit_id)
            ->sum('amount');

        if ($request->has('download')) {
            $pdf = PDF::loadView('user::customer_visits.invoicePDF.invoicePrintPDF', compact('user', 'customer_visit', 'order_details', 'total_order'));
            return $pdf->stream();
            // return $pdf->download('pdfview.pdf');
        }
    }

    public function destroyItemOrder(Request $request)
    {
        /** remove item order */
        DB::table('order_details')
            ->where('product_id', $request->id)
            ->where('visit_id', $request->visit_id)
            ->delete();

        /** update total in sales */
        $total_order = DB::table('order_details')
            ->where('order_details.visit_id', '=', $request->visit_id)
            ->sum('amount');

        Sales::where('sales.visit_id', '=', $request->visit_id)
            ->update([
                'total' => $total_order
            ]);

        /** return with response */
        if ($request->ajax()) {
            return response()->json(array(
                'success' => 'Item Order deleted successfully.',
            ));
        }
    }

    public function destroy($id)
    {
        $idRefCurrentUser = Auth::user()->idReference;

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
        $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();
        $head = 'Cancelar una Visita Cliente - #' . $customer_visit->visit_number;
        $type = 'Visita Cliente';

        if ($customer_visit->type == 'Presupuesto') {
            $linkOrderPDF = url('/user/customer_visits/generateInvoicePDF/?download=pdf&visit_id=' . $customer_visit->id);
        } else {
            $linkOrderPDF = null;
        }

        Mail::to($emailDefault)->send(new NotifyMail($customer_visit, $head, $linkOrderPDF, $type));

        return redirect()->to('/user/customer_visits')->with('message', 'Visita cliente cancelado correctamente');
    }
}
