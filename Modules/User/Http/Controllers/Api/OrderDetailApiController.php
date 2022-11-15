<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\Appointment;
use Modules\User\Entities\CustomerParameters;
use Modules\User\Entities\Customers;
use Modules\User\Entities\CustomerVisit;

use Mail;
use Modules\User\Emails\NotifyMail;
use Modules\User\Entities\OrderDetail;

class OrderDetailApiController extends Controller
{
    public function index($visit_id)
    {
        $order_details = DB::table('order_details')
            ->where('order_details.visit_id', '=', $visit_id)
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

        return response()->json(array(
            'order_details' => $order_details,
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required',
            'price' => 'required',
            'amount' => 'required',
        ]);

        $input = $request->all();

        /** Create new order detail */
        $field['visit_id'] = $input['visit_id'];
        $field['sale_id'] = null;
        $field['product_id'] = $input['product_id'];
        $field['price'] = $input['price'];
        $field['quantity'] = $input['quantity'];
        $field['amount'] = $input['amount'];
        $field['created_at'] = Carbon::now();
        $order_detail = OrderDetail::create($field);

        //return response
        return response()->json(array(
            'success' => 'Order detail created successfully.',
            'data'   => $order_detail
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

    public function search(Request $request, $idRefCurrentUser)
    {
        $search = $request->input('search');

        if ($search == '') {
            $appointments = DB::table('appointments')
                ->leftjoin('customers', 'customers.id', '=', 'appointments.customer_id')
                ->where('appointments.idReference', '=', $idRefCurrentUser)
                ->select(
                    'appointments.id',
                    'appointments.visit_number',
                    'appointments.visit_id',
                    'appointments.date',
                    'appointments.hour',
                    'appointments.action',
                    'appointments.status',
                    'appointments.observation',
                    'customers.name AS customer_name',
                    'customers.phone AS customer_phone',
                )
                ->orderBy('appointments.created_at', 'DESC')
                ->paginate(20);
        } else {
            $appointments = DB::table('appointments')
                ->leftjoin('customers', 'customers.id', '=', 'appointments.customer_id')
                ->where('customers.name', 'LIKE', "%{$search}%")
                ->orWhere('appointments.visit_number', 'LIKE', "%{$search}%")
                ->where('appointments.idReference', '=', $idRefCurrentUser)
                ->select(
                    'appointments.id',
                    'appointments.visit_number',
                    'appointments.visit_id',
                    'appointments.date',
                    'appointments.hour',
                    'appointments.action',
                    'appointments.status',
                    'appointments.observation',
                    'customers.name AS customer_name',
                    'customers.phone AS customer_phone',
                )
                ->orderBy('appointments.created_at', 'DESC')
                ->paginate();
        }

        return view('user::appointments.index', compact('appointments', 'search'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function destroy($id)
    {
        //
    }
}
