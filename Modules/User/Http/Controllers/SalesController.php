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
            ->orWhere('customer_visits.seller_id', '=', $idRefCurrentUser)
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
            ->paginate(10);

        return view('user::sales.index', compact('sales'))->with('i', (request()->input('page', 1) - 1) * 10);
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

        $request->validate([
            'customer_id' => 'required',
            'product_id' => 'required',
            'qty' => 'required',
            'price' => 'required',
            'amount' => 'required',
            'type' => 'required'
        ]);

        /** If not select any product */
        if (strlen($request->product_id[0]) > 10) {
            return back()->with('error', 'Por favor, adicione un producto.');
        } else {
            /** Save temporal sale */
            $input = $request->all();
            $input['invoice_number'] = $this->generateUniqueCode();
            $input['seller_id'] = $idRefCurrentUser;
            $input['sale_date'] = $currentDate;

            if ($input['type'] == 'Venta') {
                $input['status'] = 'Procesado';
            } elseif ($input['type'] == 'Presupuesto') {
                $input['status'] = 'Pendiente';
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
                    $order->price = $request->price[$key];
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

            if ($saleIsDeleted == false) {
                /** Get total amount from items of Sale */
                $total_order = DB::table('order_details')
                    ->where('order_details.sale_id', '=', $sale->id)
                    ->sum('amount');

                /** Update Sale with de total */
                $input['total'] = $total_order;
                $sale = Sales::find($sale->id);
                $sale->update($input);
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

        if ($sale->type == 'Venta') {
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
                ->orderBy('order_details.created_at', 'DESC')
                ->get();

            $total_order = DB::table('order_details')
                ->where('order_details.sale_id', '=', $id)
                ->sum('amount');
        } elseif ($sale->type == 'Presupuesto' && $sale->visit_id) {
            $order_detail = DB::table('order_details')
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
                ->orderBy('order_details.created_at', 'DESC')
                ->get();

            $total_order = DB::table('order_details')
                ->where('order_details.visit_id', '=', $sale->visit_id)
                ->sum('amount');
        } elseif ($sale->type == 'Presupuesto' && !$sale->visit_id) {
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
                ->orderBy('order_details.created_at', 'DESC')
                ->get();

            $total_order = DB::table('order_details')
                ->where('order_details.sale_id', '=', $id)
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

        if ($sale->type == 'Venta') {
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
                ->orderBy('order_details.created_at', 'DESC')
                ->get();

            $total_order = DB::table('order_details')
                ->where('order_details.sale_id', '=', $id)
                ->sum('amount');
        } elseif ($sale->type == 'Presupuesto' && $sale->visit_id) {
            $order_detail = DB::table('order_details')
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
                ->orderBy('order_details.created_at', 'DESC')
                ->get();

            $total_order = DB::table('order_details')
                ->where('order_details.visit_id', '=', $sale->visit_id)
                ->sum('amount');
        } elseif ($sale->type == 'Presupuesto' && !$sale->visit_id) {
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
                ->orderBy('order_details.created_at', 'DESC')
                ->get();

            $total_order = DB::table('order_details')
                ->where('order_details.sale_id', '=', $id)
                ->sum('amount');
        }

        $actions = [
            'Venta',
            'Presupuesto'
        ];

        /** Get Customers to select */
        $customers = DB::table('customers')
            ->where('customers.idReference', '=', $idRefCurrentUser)
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
        if ($request->cancelSale == true) {
            DB::table('sales')
                ->where('sales.id', '=', $request->sale_id)
                ->where('sales.invoice_number', '=', $request->invoice_number)
                ->update([
                    'status' => 'Cancelado'
                ]);

            return redirect()->to('/user/sales')->with('message', 'Venta actualizada correctamente.');
        }

        $request->validate([
            'customer_id' => 'required',
            'product_id' => 'required',
            'qty' => 'required',
            'price' => 'required',
            'amount' => 'required',
        ]);

        /** If not select any product */
        if (strlen($request->product_id[0]) > 10) {
            return back()->with('error', 'Por favor, adicione un producto.');
        } else {

            $sale = Sales::find($id);

            foreach ($request->product_id as $key => $value) {

                if (intval($request->qty[$key]) <= 0) {
                    return back()->with('error', 'Por favor, ingrese una cantidad válida.');
                } else {

                    $order_detail_id = DB::table('order_details')
                        ->where('order_details.product_id', '=', $request->product_id[$key])
                        ->where('order_details.sale_id', '=', $sale->id)
                        ->select(
                            'order_details.product_id',
                        )
                        ->first();

                    /** if find product id, update item detail; else create new item detail */
                    if ($order_detail_id) {
                        DB::table('order_details')
                            ->where('order_details.sale_id', '=', $sale->id)
                            ->where('order_details.product_id', '=', $request->product_id[$key])
                            ->update([
                                'quantity' => $request->qty[$key],
                                'amount' => $request->amount[$key]
                            ]);
                    } else {
                        /** Save item detail sale */
                        $order = new OrderDetail();
                        $order->product_id = $request->product_id[$key];
                        $order->sale_id = $sale->id;
                        $order->quantity = $request->qty[$key];
                        $order->price = $request->price[$key];
                        $order->amount = $request->amount[$key];
                        $order->save();
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
            //         $order->sale_id = $sale->id;
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
                ->where('order_details.sale_id', '=', $sale->id)
                ->sum('amount');

            /** Update Sale with total items detail and check if process or cancel sale */
            /** Order pass to Sale */
            if ($request->orderToSale == true) {
                Sales::where('sales.id', '=', $sale->id)
                    ->update([
                        'status' => 'Procesado',
                        'type' => 'Venta',
                        'total' => $total_order
                    ]);
            } elseif ($request->cancelSale == true) {
                Sales::where('sales.id', '=', $sale->id)
                    ->update([
                        'status' => 'Cancelado',
                        'total' => $total_order
                    ]);
            } else {
                Sales::where('sales.id', '=', $sale->id)
                    ->update([
                        'total' => $total_order
                    ]);
            }
        }

        return redirect()->to('/user/sales')->with('message', 'Venta actualizada correctamente.');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $idRefCurrentUser = Auth::user()->idReference;

        if ($search == '') {
            $sales = DB::table('sales')
                ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                ->Where('customers.idReference', '=', $idRefCurrentUser)
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
                ->paginate(10);
        } else {
            $sales = DB::table('sales')
                ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                ->where('customers.name', 'LIKE', "%{$search}%")
                ->Where('customers.idReference', '=', $idRefCurrentUser)
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

        return view('user::sales.index', compact('sales', 'search'))->with('i', (request()->input('page', 1) - 1) * 10);
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

        if ($sale->type == 'Venta') {
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
                ->orderBy('order_details.created_at', 'DESC')
                ->get();

            $total_order = DB::table('order_details')
                ->where('order_details.sale_id', '=', $request->saleId)
                ->sum('amount');
        } elseif ($sale->type == 'Presupuesto' && $sale->visit_id) {
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
                ->orderBy('order_details.created_at', 'DESC')
                ->get();

            $total_order = DB::table('order_details')
                ->where('order_details.visit_id', '=', $sale->visit_id)
                ->sum('amount');
        } elseif ($sale->type == 'Presupuesto' && !$sale->visit_id) {
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
                ->orderBy('order_details.created_at', 'DESC')
                ->get();

            $total_order = DB::table('order_details')
                ->where('order_details.sale_id', '=', $request->saleId)
                ->sum('amount');
        }

        $user = DB::table('users')
            ->where('users.idReference', '=', $idRefCurrentUser)
            ->select('name', 'phone_1', 'doc_id', 'address', 'email', 'city', 'estate')
            ->first();

        if ($request->has('download')) {
            $pdf = PDF::loadView('user::sales.invoicePDF.invoicePrintPDF', compact('user', 'sale', 'order_details', 'total_order'));
            return $pdf->stream();
            // return $pdf->download('pdfview.pdf');
        }
    }

    public function destroy($id)
    {
        Sales::find($id)->delete();
        return redirect()->to('/user/sales')->with('message', 'Venta eliminada correctamente');
    }
}
