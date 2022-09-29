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
            ->where('idReference', '=', $idRefCurrentUser)
            ->select(
                'id',
                'name',
                'email',
                'estate',
                'phone',
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

        $estates = array(
            array('1', 'Alto Paraná'),
            array('2', 'Central'),
            array('3', 'Concepción'),
            array('4', 'San Pedro'),
            array('5', 'Cordillera'),
            array('6', 'Guairá'),
            array('7', 'Caaguazú'),
            array('8', 'Caazapá'),
            array('9', 'Itapúa'),
            array('10', 'Misiones'),
            array('11', 'Paraguarí'),
            array('12', 'Ñeembucú'),
            array('13', 'Amambay'),
            array('14', 'Canindeyú'),
            array('15', 'Presidente Hayes'),
            array('16', 'Boquerón'),
            array('17', 'Alto Paraguay')
        );

        $userEstate = null;

        return view('user::customers.create', compact('customer', 'categories', 'potential_products', 'is_vigia_value', 'estates', 'userEstate'));
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

        Customers::create($input);

        return redirect()->to('/user/customers')->with('message', 'Customer created successfully.');
    }

    public function show($id)
    {
        $customer = Customers::find($id);

        //latitude convert in number
        $latitude = json_encode($customer->latitude);
        $latitude = str_replace('"', '', $latitude);
        $latitude = doubleval($latitude);

        //longitude convert in number
        $longitude = json_encode($customer->longitude);
        $longitude = str_replace('"', '', $longitude);
        $longitude = doubleval($longitude);

        $customerCategories =   $customer->category;
        $customerPotentialProducts =  $customer->potential_products;

        $categories = DB::table('parameters')
            ->where('type', '=', 'Rubro')
            ->select('id', 'name')
            ->orderBy('created_at', 'DESC')
            ->get();

        $potential_products = DB::table('parameters')
            ->where('type', '=', 'Equipos Potenciales')
            ->select('id', 'name')
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('user::customers.show', compact('customer', 'categories', 'potential_products', 'customerCategories', 'customerPotentialProducts', 'latitude', 'longitude'));
    }

    public function edit($id)
    {
        $customer = Customers::find($id);

        $customerCategories =  $customer->category;
        $customerPotentialProducts = $customer->potential_products;
        $userEstate = $customer->estate;

        $categories = DB::table('parameters')
            ->where('type', '=', 'Rubro')
            ->select('id', 'name')
            ->orderBy('created_at', 'DESC')
            ->get();

        $potential_products = DB::table('parameters')
            ->where('type', '=', 'Equipos Potenciales')
            ->select('id', 'name')
            ->orderBy('created_at', 'DESC')
            ->get();

        $estates = array(
            array('1', 'Alto Paraná'),
            array('2', 'Central'),
            array('3', 'Concepción'),
            array('4', 'San Pedro'),
            array('5', 'Cordillera'),
            array('6', 'Guairá'),
            array('7', 'Caaguazú'),
            array('8', 'Caazapá'),
            array('9', 'Itapúa'),
            array('10', 'Misiones'),
            array('11', 'Paraguarí'),
            array('12', 'Ñeembucú'),
            array('13', 'Amambay'),
            array('14', 'Canindeyú'),
            array('15', 'Presidente Hayes'),
            array('16', 'Boquerón'),
            array('17', 'Alto Paraguay')
        );

        return view('user::customers.edit', compact('customer', 'categories', 'potential_products', 'customerCategories', 'customerPotentialProducts', 'estates', 'userEstate'));
    }

    public function update(Request $request, $id)
    {
        /** date validation, not less than 1980 and not greater than the current year **/
        $initialDate = '1980-01-01';
        $currentDate = (date('Y') + 1) . '-01-01'; //2023-01-01

        $request->validate([
            'name' => 'required|max:50|min:5',
            'phone' => 'nullable|max:25|min:5',
            'doc_id' => 'nullable|max:25|min:5|unique:customers,doc_id,' . $id,
            'email' => 'nullable|max:50|min:5|email:rfc,dns|unique:customers,email,' . $id,
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
