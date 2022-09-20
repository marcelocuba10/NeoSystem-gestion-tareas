<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\Customers;

class CustomersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:web', ['except' => ['logout']]);

        $this->middleware('permission:customer-list|customer-create|customer-edit|customer-delete', ['only' => ['index']]);
        $this->middleware('permission:customer-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:customer-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:customer-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $idRefCurrentUser = Auth::user()->idReference;
        $customers = DB::table('customers')
            ->where('customers.idReference', '=', $idRefCurrentUser)
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

        return view('user::customers.index', compact('customers'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        $customer = null;
        $is_vigia_value = null;

        $categories = DB::table('parameters')
            ->where('type', '=', 'Rubro')
            ->get();

        $potential_products = DB::table('parameters')
            ->where('type', '=', 'Equipos Potenciales')
            ->get();

        return view('user::customers.create', compact('customer', 'categories', 'potential_products', 'is_vigia_value'));
    }

    public function store(Request $request)
    {
        /** date validation, not less than 1980 and not greater than the current year **/
        $initialDate = '1980-01-01';
        $currentDate = (date('Y') + 1) . '-01-01'; //2023-01-01


        dd($request->all());
        $request->validate([
            'name' => 'required|max:50|min:5',
            'last_name' => 'required|max:50|min:4',
            'phone' => 'nullable|max:25|min:5',
            'doc_id' => 'nullable|max:25|min:5|unique:customers,doc_id',
            'email' => 'nullable|max:25|min:5|email:rfc,dns|unique:customers,email',
            'address' => 'nullable|max:255|min:5',
            'estate' => 'nullable|max:150|min:3',
            'is_vigia' => 'nullable',
            'category' => 'nullable|max:150|min:1',
            'potential_products' => 'nullable|max:150|min:1',
            'unit_quantity' => 'nullable|integer|between:0,9999|min:0',
            'result_of_the_visit' => 'nullable|max:500|min:3',
            'objective' => 'nullable|max:500|min:3',
            'next_visit_date' => 'nullable|date_format:Y-m-d|after_or_equal:' . $initialDate . '|before:' . $currentDate,
            'next_visit_hour' => 'nullable|max:5|min:5',
        ]);

        $input = $request->all();

        /** link the customer with the admin user */
        $input['idReference'] = Auth::user()->idReference;
        Customers::create($input);

        return redirect()->to('/user/customers')->with('message', 'Customer created successfully.');
    }

    public function show($id)
    {
        $customer = DB::table('customers')
            ->where('customers.id', '=', $id)
            ->select(
                'id',
                'name',
                'last_name',
                'phone',
                'address',
                'email',
                'doc_id',
                'estate',
                'latitud',
                'longitud',
                'is_vigia',
                'category',
                'potential_products',
                'unit_quantity',
                'result_of_the_visit',
                'objective',
                'next_visit_date',
                'next_visit_hour',
            )
            ->first();

        return view('user::customers.show', compact('customer',));
    }

    public function edit($id)
    {
        $customer = DB::table('customers')
            ->where('id', '=', $id)
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
            ->first();

        $category_customer = $customer->category;
        $potential_products_customer = $customer->category;

        $categories = DB::table('parameters')
            ->where('type', '=', 'Rubro')
            ->get();

        $potential_products = DB::table('parameters')
            ->where('type', '=', 'Equipos Potenciales')
            ->get();

        return view('user::customers.edit', compact('customer', 'categories', 'potential_products', 'category_customer', 'potential_products_customer'));
    }

    public function update(Request $request, $id)
    {
        /** date validation, not less than 1980 and not greater than the current year **/
        $initialDate = '1980-01-01';
        $currentDate = (date('Y') + 1) . '-01-01'; //2023-01-01

        $request->validate([
            'name' => 'required|max:50|min:5',
            'last_name' => 'required|max:50|min:4',
            'phone' => 'nullable|max:25|min:5',
            'doc_id' => 'nullable|max:25|min:5|unique:customers,doc_id,' . $id,
            'email' => 'nullable|max:25|min:5|email:rfc,dns|unique:customers,email,' . $id,
            'address' => 'nullable|max:255|min:5',
            'estate' => 'nullable|max:150|min:3',
            'is_vigia' => 'nullable',
            'category' => 'nullable|max:150|min:1',
            'potential_products' => 'nullable|max:150|min:1',
            'unit_quantity' => 'nullable|integer|between:0,9999|min:0',
            'result_of_the_visit' => 'nullable|max:500|min:3',
            'objective' => 'nullable|max:500|min:3',
            'next_visit_date' => 'nullable|date_format:Y-m-d|after_or_equal:' . $initialDate . '|before:' . $currentDate,
            'next_visit_hour' => 'nullable|max:5|min:5',
        ]);

        $input = $request->all();

        /** if checkbox not checked */
        if ($request->is_vigia == null) {
            $input['is_vigia'] = null;
        }

        $customer = Customers::find($id);
        $customer->update($input);

        return redirect()->to('/user/customers')->with('message', 'Customer updated successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $idRefCurrentUser = Auth::user()->idReference;

        if ($search == '') {
            $customers = DB::table('customers')
                ->select('customers.id', 'customers.name', 'customers.phone', 'customers.pool', 'customers.total_machines')
                ->where('customers.idReference', '=', $idRefCurrentUser)
                ->orderBy('customers.created_at', 'DESC')
                ->paginate(10);
        } else {
            $customers = DB::table('customers')
                ->select('customers.id', 'customers.name', 'customers.phone', 'customers.pool', 'customers.total_machines')
                ->where('customers.name', 'LIKE', "%{$search}%")
                ->where('customers.idReference', '=', $idRefCurrentUser)
                ->orderBy('customers.created_at', 'DESC')
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
