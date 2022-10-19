<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\Customers;

class CustomersApiController extends Controller
{

    public function index()
    {
        // $idRefCurrentUser = Auth::user()->idReference;
        $customers = DB::table('customers')
            // ->where('idReference', '=', $idRefCurrentUser)
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json($customers);
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
            'unit_quantity' => 'nullable|integer|between:0,9999|min:0',
            'result_of_the_visit' => 'nullable|max:1000|min:3',
            'objective' => 'nullable|max:1000|min:3',
            'next_visit_date' => 'nullable|date_format:Y-m-d|after_or_equal:' . $initialDate . '|before:' . $currentDate,
            'next_visit_hour' => 'nullable|max:5|min:5',
        ]);

        $input = $request->all();

        /** link the customer with the admin user */
        $input['idReference'] = Auth::user()->idReference;

        $customer = Customers::create($input);

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
            'unit_quantity' => 'nullable|integer|between:0,9999|min:0',
            'result_of_the_visit' => 'nullable|max:1000|min:3',
            'objective' => 'nullable|max:1000|min:3',
            'next_visit_date' => 'nullable|date_format:Y-m-d|after_or_equal:today',
            'next_visit_hour' => 'nullable|max:5|min:5',
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

    public function search(Request $request)
    {
        $search = $request->input('search');
        $idRefCurrentUser = Auth::user()->idReference;

        if ($search == '') {
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
                    'unit_quantity',
                    'result_of_the_visit',
                    'next_visit_date',
                    'next_visit_hour'
                )
                ->orderBy('created_at', 'DESC')
                ->paginate(10);
        } else {
            $customers = DB::table('customers')
                ->where('idReference', '=', $idRefCurrentUser)
                ->where('name', 'LIKE', "%{$search}%")
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
                    'unit_quantity',
                    'result_of_the_visit',
                    'next_visit_date',
                    'next_visit_hour'
                )
                ->orderBy('created_at', 'DESC')
                ->paginate();
        }

        return view('user::customers.index', compact('customers', 'search'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function destroy($id)
    {
        Customers::find($id)->delete();
        return redirect()->to('/user/customers')->with('message', 'Customer deleted successfully');
    }
}
