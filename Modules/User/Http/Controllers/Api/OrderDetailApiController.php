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
use Modules\User\Entities\Sales;

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
                'order_details.product_id',
                'order_details.price',
                'order_details.quantity',
                'order_details.inventory',
                'order_details.amount'
            )
            ->orderBy('order_details.created_at', 'DESC')
            ->get();

        $total_order = DB::table('order_details')
            ->where('order_details.visit_id', '=', $visit_id)
            ->sum('amount');

        return response()->json(array(
            'order_details' => $order_details,
            'total_order' => $total_order,
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'visit_id' => 'nullable',
            'sale_id' => 'nullable',
            'product_id' => 'required',
            'quantity' => 'required',
            'price' => 'required',
            'amount' => 'required',
        ]);

        $input = $request->all();

        /** Create new order detail */
        /** Check if is Sale or Customer_visit */
        if ($input['visit_id']) {
            $field['visit_id'] = $input['visit_id'];
        }
        if ($input['sale_id']) {
            $field['sale_id'] = $input['sale_id'];
        }

        /** Extra values */
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

    public function destroyItemOrder($visit_id, $product_id)
    {
        /** remove item order */
        $order_detail = DB::table('order_details')
            ->where('product_id', $product_id)
            ->where('visit_id', $visit_id)
            ->delete();

        /** update total in sales */
        $total_order = DB::table('order_details')
            ->where('order_details.visit_id', '=', $visit_id)
            ->sum('amount');

        Sales::where('sales.visit_id', '=', $visit_id)
            ->update([
                'total' => $total_order
            ]);

        //return response
        return response()->json(array(
            'success' => 'Item Order deleted successfully.',
            'data'   => $order_detail
        ));
    }
}
