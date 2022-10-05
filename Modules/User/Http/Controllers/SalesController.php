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
use Modules\User\Entities\Sales;

class SalesController extends Controller
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

        $sales = DB::table('sales')
            ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('sales.seller_id', '=', $idRefCurrentUser)
            ->orWhere('customer_visits.seller_id', '=', $idRefCurrentUser)
            ->select(
                'sales.id',
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
        $customer_visit = null;
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

        return view('user::sales.create', compact('customers', 'customer_visit', 'currentDate', 'status', 'products'));
    }

    public function store(Request $request)
    {
        $currentDate = Carbon::now();
        $currentDate = $currentDate->toDateTimeString();

        $request->validate([
            'customer_id' => 'required',
        ]);

        $input = $request->all();
        $input['seller_id'] = Auth::user()->idReference;
        $input['sale_date'] = $currentDate;
        $input['type'] = 'Sale';
        $input['status'] = 'Procesado';
        $sale = Sales::create($input);

        $request->validate([
            'product_id' => 'required',
            'qty' => 'required|min:0',
            'price' => 'required',
            'amount' => 'required',
        ]);

        foreach ($request->product_id as $key => $value) {
            $order = new OrderDetail();
            $order->quantity = $request->qty[$key];
            $order->inventory = $request->qty_av[$key];
            $order->price = $request->price[$key];
            $order->amount = $request->amount[$key];
            $order->product_id = $request->product_id[$key];
            $order->sale_id = $sale->id;
            $order->save();
        }

        $total_order = DB::table('order_details')
            ->where('order_details.sale_id', '=', $sale->id)
            ->sum('amount');

        $input['total'] = $total_order;
        $sale = Sales::find($sale->id);
        $sale->update($input);

        return redirect()->to('/user/sales')->with('message', 'Venta Creada Correctamente');
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
                'customers.estate',
                'sales.visit_id'
            )
            ->orderBy('sales.created_at', 'DESC')
            ->first();

        $order_detail = DB::table('order_details')
            ->where('order_details.visit_id', '=', $sale->visit_id)
            ->leftjoin('products', 'products.id', '=', 'order_details.product_id')
            ->select('products.name', 'products.code', 'order_details.price', 'order_details.quantity', 'order_details.inventory', 'order_details.amount')
            ->orderBy('order_details.created_at', 'DESC')
            ->get();

        $total_order = DB::table('order_details')
            ->where('order_details.visit_id', '=', $sale->visit_id)
            ->sum('amount');

        return view('user::sales.show', compact('sale', 'order_detail', 'total_order'));
    }

    public function edit($id)
    {
        $idRefCurrentUser = Auth::user()->idReference;
        $customer_visit = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.seller_id', '=', $idRefCurrentUser)
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
                'customers.estate'
            )
            ->orderBy('customer_visits.created_at', 'DESC')
            ->first();

        $products = DB::table('products')
            ->select(
                'id',
                'name',
                'sale_price',
                'quantity',
                'description',
            )
            ->orderBy('created_at', 'DESC')
            ->get();

        $currentDate = Carbon::now();
        $currentDate = $currentDate->toDateTimeString();

        $customers = DB::table('customers')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->select('customers.id', 'customers.name')
            ->get();

        $status = [
            'Pendiente',
            'Visitado',
            'No Atendido',
            'Cancelado'
        ];

        return view('user::sales.edit', compact('customers', 'customer_visit', 'currentDate', 'status', 'products'));
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

                $input['seller_id'] = Auth::user()->idReference;
                $customer_visit = CustomerVisit::create($input);

                $request->validate([
                    'product_id' => 'required',
                    'qty' => 'required|min:0',
                    'price' => 'required',
                    'amount' => 'required',
                ]);

                foreach ($request->product_id as $key => $product_id) {
                    $item_order_visit = new OrderDetail();
                    $item_order_visit->quantity = $request->qty[$key];
                    $item_order_visit->price = $request->price[$key];
                    $item_order_visit->amount = $request->amount[$key];
                    $item_order_visit->product_id = $request->product_id[$key];
                    $item_order_visit->visit_id = $customer_visit->id;
                    $item_order_visit->update();
                }
            }
        } else {

            $customer_visit = CustomerVisit::find($id);
            $customer_visit->update($input);
        }

        return redirect()->to('/user/sales')->with('message', 'Visita Cliente actualizada correctamente.');
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

    public function destroy($id)
    {
        Sales::find($id)->delete();
        return redirect()->to('/user/sales')->with('message', 'Venta eliminada correctamente');
    }
}
