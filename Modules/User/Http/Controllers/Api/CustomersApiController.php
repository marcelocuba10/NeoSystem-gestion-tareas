<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\CustomerParameters;
use Modules\User\Entities\Customers;

class CustomersApiController extends Controller
{

    public function index($idRefCurrentUser)
    {
        $customers = DB::table('customers')
            ->where('idReference', '=', $idRefCurrentUser)
            ->orderBy('created_at', 'DESC')
            ->get();

        $categories = DB::table('parameters')
            ->select('id', 'name')
            ->where('type', '=', 'Rubro')
            ->get()
            ->toArray();

        $potential_products = DB::table('parameters')
            ->select('id', 'name')
            ->where('type', '=', 'Equipos Potenciales')
            ->get();

        return response()->json(array(
            'customers' => $customers,
            'categories' => $categories,
            'potential_products' => $potential_products,
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
        $request->validate([
            'name' => 'required|max:50|min:5',
            'phone' => 'required|max:25|min:5',
            'doc_id' => 'nullable|max:25|min:5|unique:customers,doc_id,' . $id,
            'email' => 'nullable|max:50|min:5|email:rfc,dns|unique:customers,email,' . $id,
            'address' => 'nullable|max:255|min:5',
            'city' => 'nullable|max:50|min:5',
            'estate' => 'required|max:50|min:5',
            'is_vigia' => 'nullable',
            'result_of_the_visit' => 'nullable|max:1000|min:3',
            'objective' => 'nullable|max:1000|min:3',
            'next_visit_date' => 'nullable|date_format:Y-m-d|after_or_equal:today',
            'next_visit_hour' => 'nullable|max:10|min:5',
        ]);

        $input = $request->all();

        /** if checkbox not checked */
        if ($request->is_vigia == null) {
            $input['is_vigia'] = null;
        }

        //update in DB
        $customer = Customers::find($id);
        $input['category'] = str_replace('/\/', '', $customer->category);
        $input['potential_products'] = str_replace('/\/', '', $customer->potential_products);
        $customer->update($input);

        //return response
        return response()->json(array(
            'success' => 'Customer updated successfully.',
            'data'   => $customer
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
