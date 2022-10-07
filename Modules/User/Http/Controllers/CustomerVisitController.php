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
use Symfony\Component\Console\Input\Input;

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
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.status',
                'customer_visits.type',
                'customers.name AS customer_name',
                'customers.estate'
            )
            ->orderBy('customer_visits.created_at', 'DESC')
            ->paginate(10);

        return view('user::customer_visits.index', compact('customer_visits'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        $customer_visit = null;
        $customer_visit_type = null;
        $currentDate = Carbon::now();
        $currentDate = $currentDate->toDateTimeString();

        $idRefCurrentUser = Auth::user()->idReference;

        $customers = DB::table('customers')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->select('customers.id', 'customers.name')
            ->get();

        $status = [
            'Visitado',
            'No Atendido',
            'Cancelado'
        ];

        $products = DB::table('products')
            ->select(
                'id',
                'name',
                'sale_price',
                'inventory',
                'description',
            )
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('user::customer_visits.create', compact('customers', 'customer_visit', 'currentDate', 'status', 'products', 'customer_visit_type'));
    }

    public function store(Request $request)
    {
        /** date validation, not less than 1980 and not greater than the current year **/
        $initialDate = '1980-01-01';
        $currentDate = (date('Y') + 1) . '-01-01'; //2023-01-01

        $request->validate([
            'customer_id' => 'required',
            'visit_date' => 'nullable|date|after_or_equal:today|before:' . $currentDate,
            'next_visit_date' => 'nullable|date_format:Y-m-d|after_or_equal:today|before:' . $currentDate,
            'next_visit_hour' => 'nullable|max:5|min:5',
            'status' => 'required|max:30|min:5',
            'result_of_the_visit' => 'nullable|max:1000|min:3',
            'objective' => 'nullable|max:1000|min:3',
            'product_id' => 'required',
            'qty' => 'required',
            'price' => 'required',
            'amount' => 'required',
        ]);

        $input = $request->all();

        if ($input['next_visit_date'] == null) {
            $input['next_visit_date'] = 'No marcado';
        }

        if ($input['next_visit_hour'] == null) {
            $input['next_visit_hour'] = 'No marcado';
        }

        /** check if checkbox is checked */
        if (isset($request->setOrder)) {
            /** If not select any product */
            if (strlen($request->product_id[0]) > 10) {
                return back()->with('error', 'Por favor, adicione un producto.');
            } else {

                /** Save temporal CustomerVisit */
                $input['type'] = 'Order';
                $input['seller_id'] = Auth::user()->idReference;
                $customer_visit = CustomerVisit::create($input);

                foreach ($request->product_id as $key => $value) {

                    $product = DB::table('products')
                        ->where('products.id', '=', $request->product_id[$key])
                        ->select(
                            'products.id',
                            'products.inventory',
                        )
                        ->first();

                    if (intval($request->qty[$key]) > $product->inventory || intval($request->qty[$key]) <= 0) {
                        $CustomerVisitIsDeleted = CustomerVisit::find($customer_visit->id)->delete();
                        return back()->with('error', 'Por favor, ingrese una cantidad válida.');
                    } else {
                        /** Save items of sale */
                        $order = new OrderDetail();
                        $order->quantity = $request->qty[$key];
                        $order->inventory = $request->qty_av[$key];
                        $order->price = $request->price[$key];
                        $order->amount = $request->amount[$key];
                        $order->product_id = $request->product_id[$key];
                        $order->visit_id = $customer_visit->id;
                        $order->save();

                        /** if there is an error in items, customer_visit is deleted */
                        $CustomerVisitIsDeleted = false;
                    }
                }

                if ($CustomerVisitIsDeleted == false) {
                    $total_order = DB::table('order_details')
                        ->where('order_details.visit_id', '=', $customer_visit->id)
                        ->sum('amount');

                    $sale['visit_id'] = $customer_visit->id;
                    $sale['seller_id'] = Auth::user()->idReference;
                    $sale['customer_id'] = $customer_visit->customer_id;
                    $sale['order_date'] = $customer_visit->visit_date;
                    $sale['type'] = 'Order';
                    $sale['status'] = 'Pendiente';
                    $sale['total'] = $total_order;
                    Sales::create($sale);
                }
            }
        } else {

            $input['type'] = 'NoOrder';
            $input['seller_id'] = Auth::user()->idReference;
            $customer_visit = CustomerVisit::create($input);
        }

        return redirect()->to('/user/customer_visits')->with('message', 'Visita Cliente Creada Correctamente');
    }

    public function show($id)
    {
        $idRefCurrentUser = Auth::user()->idReference;
        $customer_visit = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.id', '=', $id)
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
                'customers.estate'
            )
            ->orderBy('customer_visits.created_at', 'DESC')
            ->first();

        $order_details = DB::table('order_details')
            ->where('order_details.visit_id', '=', $id)
            ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
            ->select('products.name', 'products.code', 'order_details.price', 'order_details.quantity', 'order_details.inventory', 'order_details.amount')
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
                'customer_visits.customer_id',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.next_visit_hour',
                'customer_visits.result_of_the_visit',
                'customer_visits.objective',
                'customer_visits.status',
                'customers.name AS customer_name',
                'customers.estate',
                'customer_visits.type'
            )
            ->orderBy('customer_visits.created_at', 'DESC')
            ->first();

        $customer_visit_type = $customer_visit->type;

        $products = DB::table('products')
            ->select(
                'id',
                'name',
                'sale_price',
                'inventory',
                'description',
            )
            ->orderBy('created_at', 'DESC')
            ->get();

        $customers = DB::table('customers')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->select('customers.id', 'customers.name')
            ->get();

        $status = [
            'Visitado',
            'No Atendido',
            'Cancelado'
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

        return view('user::customer_visits.edit', compact('customers', 'customer_visit', 'currentDate', 'status', 'products', 'order_details', 'total_order', 'customer_visit_type'));
    }

    public function update(Request $request, $id)
    {
        /** date validation, not less than 1980 and not greater than the current year **/
        $initialDate = '1980-01-01';
        $currentDate = (date('Y') + 1) . '-01-01'; //2023-01-01

        $request->validate([
            'customer_id' => 'required',
            'visit_date' => 'nullable|date|after_or_equal:today|before:' . $currentDate,
            'next_visit_date' => 'nullable|date_format:Y-m-d|after_or_equal:today|before:' . $currentDate,
            'next_visit_hour' => 'nullable|max:5|min:5',
            'status' => 'required|max:30|min:5',
            'result_of_the_visit' => 'nullable|max:1000|min:3',
            'objective' => 'nullable|max:1000|min:3',
            'product_id' => 'required',
            'qty' => 'required|min:0',
            'price' => 'required',
            'amount' => 'required',
        ]);

        $input = $request->all();

        if ($input['next_visit_date'] == null) {
            $input['next_visit_date'] = 'No marcado';
        }

        if ($input['next_visit_hour'] == null) {
            $input['next_visit_hour'] = 'No marcado';
        }

        /** check if checkbox is checked */
        if (isset($request->setOrder)) {
            /** If not select any product */
            if (strlen($request->product_id[0]) > 10) {
                return back()->with('error', 'Por favor, adicione un producto.');
            } else {

                $customer_visit = CustomerVisit::find($id);

                if ($customer_visit->type == 'NoOrder') {
                    foreach ($request->product_id as $key => $value) {

                        $product = DB::table('products')
                            ->where('products.id', '=', $request->product_id[$key])
                            ->select(
                                'products.id',
                                'products.inventory',
                            )
                            ->first();

                        if (intval($request->qty[$key]) > $product->inventory || intval($request->qty[$key]) <= 0) {
                            return back()->with('error', 'Por favor, ingrese una cantidad válida.');
                        } else {
                            /** Save items of customer visit */
                            $order = new OrderDetail();
                            $order->quantity = $request->qty[$key];
                            $order->inventory = $request->qty_av[$key];
                            $order->price = $request->price[$key];
                            $order->amount = $request->amount[$key];
                            $order->product_id = $request->product_id[$key];
                            $order->visit_id = $customer_visit->id;
                            $order->save();
                        }
                    }

                    $total_order = DB::table('order_details')
                        ->where('order_details.visit_id', '=', $customer_visit->id)
                        ->sum('amount');

                    $sale['visit_id'] = $customer_visit->id;
                    $sale['seller_id'] = Auth::user()->idReference;
                    $sale['customer_id'] = $customer_visit->customer_id;
                    $sale['order_date'] = $customer_visit->visit_date;
                    $sale['type'] = 'Order';
                    $sale['status'] = 'Pendiente';
                    $sale['total'] = $total_order;
                    Sales::create($sale);
                } else {

                    foreach ($request->product_id as $key => $value) {

                        $product = DB::table('products')
                            ->where('products.id', '=', $request->product_id[$key])
                            ->select(
                                'products.id',
                                'products.inventory',
                            )
                            ->first();

                        if (intval($request->qty[$key]) > $product->inventory || intval($request->qty[$key]) <= 0) {
                            return back()->with('error', 'Por favor, ingrese una cantidad válida.');
                        } else {
                            /** update items of customer visit */
                            $order = new OrderDetail();
                            $order->quantity = $request->qty[$key];
                            $order->inventory = $request->qty_av[$key];
                            $order->price = $request->price[$key];
                            $order->amount = $request->amount[$key];
                            $order->product_id = $request->product_id[$key];
                            $order->visit_id = $customer_visit->id;
                            $order->update();
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
                $input['type'] = 'Order';
                $input['seller_id'] = Auth::user()->idReference;
                $customer_visit->update($input);
            }
        } else {
            $customer_visit = CustomerVisit::find($id);
            $customer_visit->update($input);
        }

        return redirect()->to('/user/customer_visits')->with('message', 'Visita Cliente actualizada correctamente.');
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
                    'customer_visits.visit_date',
                    'customer_visits.next_visit_date',
                    'customer_visits.next_visit_hour',
                    'customer_visits.result_of_the_visit',
                    'customer_visits.objective',
                    'customer_visits.status',
                    'customer_visits.type',
                    'customers.name AS customer_name',
                    'customers.estate'
                )
                ->orderBy('customer_visits.created_at', 'DESC')
                ->paginate(10);
        } else {
            $customer_visits = DB::table('customer_visits')
                ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                ->where('customers.idReference', '=', $idRefCurrentUser)
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
                    'customers.estate'
                )
                ->orderBy('customer_visits.created_at', 'DESC')
                ->paginate();
        }

        return view('user::customer_visits.index', compact('customer_visits', 'search'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function destroy($id)
    {
        CustomerVisit::find($id)->delete();
        return redirect()->to('/user/customer_visits')->with('message', 'Customer deleted successfully');
    }
}
