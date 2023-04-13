<?php

namespace Modules\User\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\CustomerVisit;
use Modules\User\Entities\OrderDetail;
use Modules\User\Entities\Products;
use Modules\User\Entities\Sales;

use PDF;
use Mail;
use Modules\User\Emails\NotifyMail;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web', ['except' => ['logout']]);

        $this->middleware('permission:sales-list|sales-create|sales-edit|sales-delete', ['only' => ['index']]);
        $this->middleware('permission:sales-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:sales-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:sales-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $idRefCurrentUser = Auth::user()->idReference;

        $sales = DB::table('sales')
            ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('sales.seller_id', '=', $idRefCurrentUser)
            ->where('sales.isTemp', '!=', 1)
            ->select(
                'sales.id',
                'sales.customer_id',
                'sales.invoice_number',
                'sales.sale_date',
                'sales.type',
                'sales.status',
                'sales.total',
                'customers.name AS customer_name',
                'customers.estate',
                'customer_visits.visit_date',
            )
            ->orderBy('sales.created_at', 'DESC')
            ->paginate(20);

        return view('user::sales.index', compact('sales'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function create()
    {
        /** Auxiliar Variables */
        $sale = null;

        /** Format Date */
        $currentDate = Carbon::now();
        $currentDate = $currentDate->toDateTimeString();

        /** Get current ID user */
        $idRefCurrentUser = Auth::user()->idReference;

        /** Get Customers to select */
        $customers = DB::table('customers')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->where('customers.status', '=', 1)
            ->select('customers.id', 'customers.name')
            ->get();

        /** Get Products to select */
        $products = DB::table('products')
            ->select(
                'id',
                'name',
                'sale_price',
            )
            ->orderBy('created_at', 'DESC')
            ->get();

        $actions = [
            'Venta',
            'Presupuesto'
        ];

        return view('user::sales.create', compact('actions', 'sale', 'customers', 'currentDate', 'products'));
    }

    public function store(Request $request)
    {
        /** Format Date */
        $currentDate = Carbon::now();
        $currentDate = $currentDate->toDateTimeString();

        /** Get current ID user */
        $idRefCurrentUser = Auth::user()->idReference;

        $request->validate(
            [
                'customer_id' => 'required',
                'product_id' => 'required',
                'qty' => 'required',
                'price' => 'required',
                'amount' => 'required',
                'type' => 'required'
            ],
            [
                'customer_id.required'  => 'El campo Cliente es obligatorio.'
            ]
        );

        /** If not select any product */
        if (strlen($request->product_id[0]) > 10) {
            return back()->with('error', 'Por favor, adicione un producto.');
        } else {
            /** Save temporal sale */
            $input = $request->all();
            $input['invoice_number'] = $this->generateUniqueCode();
            $input['seller_id'] = $idRefCurrentUser;
            $input['sale_date'] = $currentDate;
            $input['isTemp'] = 1;

            if ($input['type'] == 'Venta') {
                $input['status'] = 'Procesado';
                $input['previous_type'] = 'Venta';
            } elseif ($input['type'] == 'Presupuesto') {
                $input['status'] = 'Pendiente';
                $input['previous_type'] = 'Presupuesto';
            }

            $sale = Sales::create($input);

            foreach ($request->product_id as $key => $value) {

                if (intval($request->qty[$key]) <= 0) {
                    return back()->with('error', 'Por favor, ingrese una cantidad válida.');
                } else {
                    /** Save items of sale */
                    $order = new OrderDetail();
                    $order->sale_id = $sale->id;
                    $order->product_id = $request->product_id[$key];
                    $order->quantity = $request->qty[$key];
                    $order->price = str_replace(',', '', $request->price[$key]);
                    $order->amount = $request->amount[$key];
                    $order->save();

                    $saleIsDeleted = false;
                }

                // $product = DB::table('products')
                //     ->where('products.id', '=', $request->product_id[$key])
                //     ->select(
                //         'products.id',
                //         'products.inventory',
                //     )
                //     ->first();

                // if (intval($request->qty[$key]) > $product->inventory || intval($request->qty[$key]) <= 0) {
                //     $saleIsDeleted = Sales::find($sale->id)->delete();
                //     return back()->with('error', 'Por favor, ingrese una cantidad válida.');
                // } else {
                //     /** Save items of sale */
                //     $order = new OrderDetail();
                //     $order->quantity = $request->qty[$key];
                //     $order->inventory = $request->qty_av[$key];
                //     $order->price = $request->price[$key];
                //     $order->amount = $request->amount[$key];
                //     $order->product_id = $request->product_id[$key];
                //     $order->sale_id = $sale->id;
                //     $order->save();

                //     /** Discount inventory from product */
                //     $product_inventory = $product->inventory - $request->qty[$key];

                //     Products::where('products.id', '=', $request->product_id[$key])
                //         ->update([
                //             'products.inventory' => $product_inventory
                //         ]);

                //     /** if there is an error in items, sale is deleted */
                //     $saleIsDeleted = false;
                // }
            }

            /** If everything is ok, proceed with the operation */
            if ($saleIsDeleted == false) {
                /** Get total amount from items of Sale */
                $total_order = DB::table('order_details')
                    ->where('order_details.sale_id', '=', $sale->id)
                    ->sum('amount');

                /** Update Sale with de total */
                $input['total'] = $total_order;
                $input['isTemp'] = 0;
                $sale = Sales::find($sale->id);
                $sale->update($input);

                /** Send email notification - updated status sale to process*/
                $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();
                $head = 'crear un(a) ' . $sale->type . ' - #' . $sale->invoice_number;
                $type = 'Venta';
                //** create link to download pdf invoice in email */
                $linkOrderPDF = url('/sales/' . $idRefCurrentUser . '/generateInvoicePDF/?download=pdf&saleId=' . $sale->id);

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

        return redirect()->to('/user/sales')->with('message', 'Venta Creada Correctamente');
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

    public function show($id)
    {
        $idRefCurrentUser = Auth::user()->idReference;
        $sale = DB::table('sales')
            ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('sales.id', '=', $id)
            ->select(
                'customer_visits.id',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.next_visit_hour',
                'customer_visits.result_of_the_visit',
                'customer_visits.objective',
                'customer_visits.status',
                'customers.name AS customer_name',
                'sales.invoice_number',
                'sales.visit_id',
                'sales.type',
                'sales.status',
                'sales.sale_date'
            )
            ->orderBy('sales.created_at', 'DESC')
            ->first();

        if (!$sale->visit_id) {
            $order_detail = DB::table('order_details')
                ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
                ->where('order_details.sale_id', '=', $id)
                ->select(
                    'order_details.product_id',
                    'order_details.price',
                    'order_details.quantity',
                    'order_details.amount',
                    'products.custom_code',
                    'products.name',
                )
                ->orderBy('order_details.created_at', 'ASC')
                ->get();

            $total_order = DB::table('order_details')
                ->where('order_details.sale_id', '=', $id)
                ->sum('amount');
        }

        if ($sale->visit_id) {
            $order_detail = DB::table('order_details')
                ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
                ->where('order_details.visit_id', '=', $sale->visit_id)
                ->select(
                    'order_details.product_id',
                    'order_details.price',
                    'order_details.quantity',
                    'order_details.amount',
                    'products.custom_code',
                    'products.name',
                )
                ->orderBy('order_details.created_at', 'ASC')
                ->get();

            $total_order = DB::table('order_details')
                ->where('order_details.visit_id', '=', $sale->visit_id)
                ->sum('amount');
        }

        return view('user::sales.show', compact('sale', 'order_detail', 'total_order'));
    }

    public function edit($id)
    {
        $idRefCurrentUser = Auth::user()->idReference;

        /** Format Date */
        $currentDate = Carbon::now();
        $currentDate = $currentDate->toDateTimeString();

        $sale = DB::table('sales')
            ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('sales.id', '=', $id)
            ->select(
                'customer_visits.id',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.next_visit_hour',
                'customer_visits.result_of_the_visit',
                'customer_visits.objective',
                'customer_visits.status',
                'customers.name AS customer_name',
                'sales.invoice_number',
                'sales.visit_id',
                'sales.customer_id',
                'sales.type',
                'sales.sale_date',
                'sales.id',
                'sales.status',
            )
            ->orderBy('sales.created_at', 'DESC')
            ->first();

        if (!$sale->visit_id) {
            $order_detail = DB::table('order_details')
                ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
                ->where('order_details.sale_id', '=', $id)
                ->select(
                    'order_details.product_id',
                    'order_details.price',
                    'order_details.quantity',
                    'order_details.amount',
                    'products.custom_code',
                    'products.name',
                )
                ->orderBy('order_details.created_at', 'ASC')
                ->get();

            $total_order = DB::table('order_details')
                ->where('order_details.sale_id', '=', $id)
                ->sum('amount');
        }

        if ($sale->visit_id) {
            $order_detail = DB::table('order_details')
                ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
                ->where('order_details.visit_id', '=', $sale->visit_id)
                ->select(
                    'order_details.product_id',
                    'order_details.price',
                    'order_details.quantity',
                    'order_details.amount',
                    'products.custom_code',
                    'products.name',
                )
                ->orderBy('order_details.created_at', 'ASC')
                ->get();

            $total_order = DB::table('order_details')
                ->where('order_details.visit_id', '=', $sale->visit_id)
                ->sum('amount');
        }

        $actions = [
            'Venta',
            'Presupuesto'
        ];

        /** Get Customers to select */
        $customers = DB::table('customers')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->where('customers.status', '=', 1)
            ->select('customers.id', 'customers.name')
            ->get();

        /** Get Products to select */
        $products = DB::table('products')
            ->select(
                'id',
                'custom_code',
                'name',
                'sale_price',
            )
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('user::sales.edit', compact('products', 'currentDate', 'customers', 'actions', 'sale', 'order_detail', 'total_order'));
    }

    public function update(Request $request, $id)
    {
        /** Get IDreference current user */
        $idRefCurrentUser = Auth::user()->idReference;

        /** Get sale by id */
        $sale = Sales::find($id);

        /** Cancel sale direct, without customer visit, update status */
        if ($sale->type == 'Venta' && !$sale->visit_id) {

            /** a sale cannot be edited, since it was processed, only the cancel button */
            if ($request->cancelSale == true) {
                DB::table('sales')
                    ->where('sales.id', '=', $request->id)
                    ->where('sales.invoice_number', '=', $request->invoice_number)
                    ->where('sales.seller_id', '=', $idRefCurrentUser)
                    ->update([
                        'status' => 'Cancelado'
                    ]);

                /** Get sale info to notify email*/
                $sale = DB::table('sales')
                    ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                    ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                    ->leftjoin('users', 'users.idReference', '=', 'sales.seller_id')
                    ->where('sales.id', $id)
                    ->select(
                        'sales.id',
                        'sales.invoice_number',
                        'sales.sale_date',
                        'sales.type',
                        'sales.status',
                        'sales.total',
                        'sales.visit_id',
                        'sales.previous_type',
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

                return redirect()->to('/user/sales')->with('message', 'Registro cancelado correctamente.');
            }
        }

        /** Cancel sale, relationship with customer visit, update two status in every table */
        if ($sale->type == 'Venta' && $sale->visit_id) {

            /** when the sale comes to visit, no change is verified here since it is verified in customer visits, here only button cancel */
            if ($request->cancelSale == true) {
                /** update status in sales and customer_visit */
                DB::table('sales')
                    ->where('sales.id', '=', $request->id)
                    ->where('sales.invoice_number', '=', $request->invoice_number)
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

                /** Get sale info to notify email*/
                $sale = DB::table('sales')
                    ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                    ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                    ->leftjoin('users', 'users.idReference', '=', 'sales.seller_id')
                    ->where('sales.id', $id)
                    ->select(
                        'sales.id',
                        'sales.invoice_number',
                        'sales.sale_date',
                        'sales.type',
                        'sales.status',
                        'sales.total',
                        'sales.visit_id',
                        'sales.previous_type',
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
                $linkOrderPDF = url('/sales/' . $idRefCurrentUser . '/generateInvoicePDF/?download=pdf&saleId=' . $id);

                Mail::to($emailDefault)->send(new NotifyMail($sale, $head, $linkOrderPDF, $type));

                return redirect()->to('/user/sales')->with('message', 'Registro cancelado correctamente.');
            }
        }

        /** if it is a order created from sales, check validations and process */
        if ($sale->type == 'Presupuesto' && !$sale->visit_id) {
            $request->validate([
                'customer_id' => 'required',
                'product_id' => 'required',
                'qty' => 'required',
                'price' => 'required',
                'amount' => 'required',
            ]);

            $input = $request->all();

            /** If not select any product */
            if (strlen($request->product_id[0]) > 10) {
                return back()->with('error', 'Por favor, adicione un producto.');
            } else {

                /** Get old items saved in order details in array*/
                $order_details = DB::table('order_details')
                    ->where('order_details.sale_id', '=', $id)
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
                            /** Save item detail sale */
                            $order = new OrderDetail();
                            $order->product_id = $request->product_id[$key];
                            $order->sale_id = $id;
                            $order->quantity = $request->qty[$key];
                            $order->price = str_replace(',', '', $request->price[$key]);
                            $order->amount = $request->amount[$key];
                            $order->save();
                        }
                    }
                } else {
                    /** Array not have news product_id, update values in order details */
                    foreach ($request->product_id as $key => $value) {

                        if (intval($request->qty[$key]) <= 0) {
                            return back()->with('error', 'Por favor, ingrese una cantidad válida.');
                        } else {

                            /** if find product id, if exist product_id, update values else delete old product_id and create new item detail with the new product_id */
                            $order_detail_id = DB::table('order_details')
                                ->where('order_details.product_id', '=', $request->product_id[$key])
                                ->where('order_details.sale_id', '=', $id)
                                ->select(
                                    'order_details.product_id',
                                )
                                ->first();

                            if ($order_detail_id) {
                                DB::table('order_details')
                                    ->where('order_details.sale_id', '=', $id)
                                    ->where('order_details.product_id', '=', $request->product_id[$key])
                                    ->update([
                                        'quantity' => $request->qty[$key],
                                        'amount' => $request->amount[$key]
                                    ]);
                            } else {
                                /** Save item detail sale */
                                $order = new OrderDetail();
                                $order->product_id = $request->product_id[$key];
                                $order->sale_id = $id;
                                $order->quantity = $request->qty[$key];
                                $order->price = str_replace(',', '', $request->price[$key]);
                                $order->amount = $request->amount[$key];
                                $order->save();
                            }
                        }
                    }
                }

                /** old code with inventary discount */
                // foreach ($request->product_id as $key => $value) {
                //     $product = DB::table('products')
                //         ->where('products.custom_code', '=', $request->product_id[$key])
                //         ->select(
                //             'products.id',
                //             'products.inventory',
                //         )
                //         ->first();

                //     if (intval($request->qty[$key]) > $product->inventory || intval($request->qty[$key]) <= 0) {
                //         return back()->with('error', 'Por favor, ingrese una cantidad válida.');
                //     } else {
                //         /** update items of sale */
                //         $order = new OrderDetail();
                //         $order->quantity = $request->qty[$key];
                //         $order->inventory = $request->qty_av[$key];
                //         $order->price = $request->price[$key];
                //         $order->amount = $request->amount[$key];
                //         $order->product_id = $request->product_id[$key];
                //         $order->sale_id = $id;
                //         $order->update();

                //         /** Discount inventory from product */
                //         $product_inventory = $product->inventory - $request->qty[$key];

                //         Products::where('products.id', '=', $request->product_id[$key])
                //             ->update([
                //                 'products.inventory' => $product_inventory
                //             ]);
                //     }
                // }

                /** Get total amount from items of Sale */
                $total_order = DB::table('order_details')
                    ->where('order_details.sale_id', '=', $id)
                    ->sum('amount');

                /** Update Sale with total items detail and check if process or cancel sale */
                /** Order pass to Sale */
                if ($request->orderToSale == true) {
                    Sales::where('sales.id', '=', $id)
                        ->update([
                            'status' => 'Procesado',
                            'type' => 'Venta',
                        ]);

                    /** Send email notification - updated status sale to process*/
                    $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();
                    $head = 'procesar un ' . $sale->previous_type . ' para Venta - #' . $sale->invoice_number;
                    $type = 'Venta';
                    //** create link to download pdf invoice in email */
                    $linkOrderPDF = url('/sales/' . $idRefCurrentUser . '/generateInvoicePDF/?download=pdf&saleId=' . $id);

                    $sale = DB::table('sales')
                        ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                        ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                        ->leftjoin('users', 'users.idReference', '=', 'sales.seller_id')
                        ->where('sales.id', $id)
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

                if ($request->cancelSale == true) {
                    Sales::where('sales.id', '=', $id)
                        ->update([
                            'status' => 'Cancelado',
                        ]);
                }

                /** Update Sale */
                Sales::where('sales.id', '=', $id)
                    ->update([
                        'customer_id' => $input['customer_id'],
                        'total' => $total_order
                    ]);
            }
        }

        /** if is a Order created by customer_visit, ignore validations and update the status */
        if ($sale->type == 'Presupuesto' && $sale->visit_id) {

            /** Here update status to process this is created by customer_visit, ignore validations */
            /** check if have changes in order details, get total order */
            $total_order = DB::table('order_details')
                ->where('order_details.visit_id', '=', $sale->visit_id)
                ->sum('amount');

            /** Update Sale with total items detail and check if process or cancel sale */
            /** Order pass to Sale */
            if ($request['orderToSale'] == true) {
                /** update status in sales and customer_visit */
                Sales::where('sales.id', '=', $id)
                    ->update([
                        'status' => 'Procesado',
                        'type' => 'Venta',
                        'total' => $total_order
                    ]);

                DB::table('customer_visits')
                    ->where('customer_visits.id', '=', $sale->visit_id)
                    ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
                    ->update([
                        'status' => 'Procesado',
                    ]);

                /** Send email notification - updated status sale to process*/
                $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();
                $head = 'procesar un ' . $sale->previous_type . ' para Venta - #' . $sale->invoice_number;
                $type = 'Venta';
                //** create link to download pdf invoice in email */
                $linkOrderPDF = url('/sales/' . $idRefCurrentUser . '/generateInvoicePDF/?download=pdf&saleId=' . $id);

                $sale = DB::table('sales')
                    ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                    ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                    ->leftjoin('users', 'users.idReference', '=', 'sales.seller_id')
                    ->where('sales.id', $id)
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

        return back()->with('message', 'Cambios realizados correctamente.');
        //return redirect()->to('/user/sales')->with('message', 'Venta actualizada correctamente.');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $idRefCurrentUser = Auth::user()->idReference;

        if ($search == '') {

            $sales = DB::table('sales')
                ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                ->where('sales.seller_id', '=', $idRefCurrentUser)
                ->where('sales.isTemp', '!=', 1)
                ->select(
                    'sales.id',
                    'sales.customer_id',
                    'sales.invoice_number',
                    'sales.sale_date',
                    'sales.type',
                    'sales.status',
                    'sales.total',
                    'customers.name AS customer_name',
                    'customers.estate',
                    'customer_visits.visit_date',
                )
                ->orderBy('sales.created_at', 'DESC')
                ->paginate(20);
        } else {
            $sales = DB::table('sales')
                ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                ->where('customers.name', 'LIKE', "%{$search}%")
                ->where('sales.isTemp', '!=', 1)
                ->orWhere('sales.invoice_number', 'LIKE', "%{$search}%")
                ->where('sales.seller_id', '=', $idRefCurrentUser)
                ->select(
                    'sales.id',
                    'sales.invoice_number',
                    'sales.sale_date',
                    'sales.type',
                    'sales.status',
                    'sales.total',
                    'customers.name AS customer_name',
                    'customers.estate',
                    'customer_visits.visit_date',
                )
                ->orderBy('sales.created_at', 'DESC')
                ->paginate();
        }

        return view('user::sales.index', compact('sales', 'search'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function generateInvoicePDF(Request $request)
    {
        $idRefCurrentUser = Auth::user()->idReference;

        $sale = DB::table('sales')
            ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('sales.id', '=', $request->saleId)
            ->select(
                'customer_visits.id',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.next_visit_hour',
                'customer_visits.result_of_the_visit',
                'customer_visits.objective',
                'customer_visits.status',
                'customers.name AS customer_name',
                'customers.estate',
                'customers.doc_id',
                'customers.email',
                'customers.phone',
                'customers.address',
                'sales.invoice_number',
                'sales.visit_id',
                'sales.type',
                'sales.status',
                'sales.sale_date',
                'sales.order_date'
            )
            ->orderBy('sales.created_at', 'DESC')
            ->first();

        if (!$sale->visit_id) {
            $order_details = DB::table('order_details')
                ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
                ->where('order_details.sale_id', '=', $request->saleId)
                ->select(
                    'order_details.product_id',
                    'order_details.price',
                    'order_details.quantity',
                    'order_details.amount',
                    'products.custom_code',
                    'products.name',
                )
                ->orderBy('order_details.created_at', 'ASC')
                ->get();

            $total_order = DB::table('order_details')
                ->where('order_details.sale_id', '=', $request->saleId)
                ->sum('amount');
        } elseif ($sale->visit_id) {
            $order_details = DB::table('order_details')
                ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
                ->where('order_details.visit_id', '=', $sale->visit_id)
                ->select(
                    'order_details.product_id',
                    'order_details.price',
                    'order_details.quantity',
                    'order_details.amount',
                    'products.name',
                    'products.custom_code',
                )
                ->orderBy('order_details.created_at', 'ASC')
                ->get();

            $total_order = DB::table('order_details')
                ->where('order_details.visit_id', '=', $sale->visit_id)
                ->sum('amount');
        }

        $user = DB::table('users')
            ->where('users.idReference', '=', $idRefCurrentUser)
            ->select('name', 'phone_1', 'doc_id', 'address', 'email', 'city', 'estate')
            ->first();

        if ($request->has('download')) {
            $pdf = PDF::loadView('user::sales.invoicePDF.invoicePrintPDF', compact('user', 'sale', 'order_details', 'total_order'));
            //return $pdf->stream();
            return $pdf->download('Documento-'.$sale->invoice_number.'.pdf');
        }
    }

    public function dataAjax(Request $request)
    {
        $data = [];

        if ($request->has('q')) {
            $search = $request->q;
            $data = Products::select("id", "name")
                ->where('name', 'LIKE', "%$search%")
                ->get();
        }
        return response()->json($data);
    }

    public function destroyItemOrder(Request $request)
    {
        /** remove item order */
        DB::table('order_details')
            ->where('product_id', $request->id)
            ->where('sale_id', $request->sale_id)
            ->delete();

        /** update total in sales */
        $total_order = DB::table('order_details')
            ->where('order_details.sale_id', '=', $request->sale_id)
            ->sum('amount');

        Sales::where('sales.id', '=', $request->sale_id)
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

        /** Get sale by id */
        $sale = Sales::find($id);

        /** check if sale/order relation with visit_customer, update status to cancel */
        if (!$sale->visit_id) {
            DB::table('sales')
                ->where('sales.id', '=', $id)
                ->where('sales.seller_id', '=', $idRefCurrentUser)
                ->update([
                    'status' => 'Cancelado'
                ]);
        } else {
            DB::table('sales')
                ->where('sales.id', '=', $id)
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
            ->where('sales.id', $id)
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

        return redirect()->to('/user/sales')->with('message', 'Registro cancelado correctamente');
    }
}
