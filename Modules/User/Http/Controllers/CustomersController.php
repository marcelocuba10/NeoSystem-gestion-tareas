<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\CustomerParameters;
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
                'category'
            )
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        $categories = DB::table('parameters')
            ->where('type', '=', 'Rubro')
            ->select('id', 'name')
            ->get();

        return view('user::customers.index', compact('customers', 'categories'))->with('i', (request()->input('page', 1) - 1) * 10);
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
        $currentDate = (date('Y') + 2) . '-01-01'; //current year + 2 year 

        $request->validate([
            'name' => 'required|max:50|min:5',
            'phone' => 'required|max:25|min:5',
            'doc_id' => 'nullable|max:25|min:5|unique:customers,doc_id',
            'email' => 'nullable|max:50|min:5|email:rfc,dns|unique:customers,email',
            'address' => 'nullable|max:255|min:5',
            'city' => 'nullable|max:50|min:5',
            'estate' => 'required|max:50|min:5',
            'is_vigia' => 'nullable',
            'category' => 'required|max:150|min:1',
            'potential_products' => 'required|max:150|min:1',
            'result_of_the_visit' => 'required|max:1000|min:3',
            'objective' => 'required|max:1000|min:3',
            'next_visit_date' => 'nullable|date_format:Y-m-d|after_or_equal:' . $initialDate . '|before:' . $currentDate,
            'next_visit_hour' => 'nullable|max:5|min:5',
        ]);

        $input = $request->all();

        /** idReference is the unique code of the current User */
        $input['idReference'] = Auth::user()->idReference;

        /** If not select any potential product */
        if (strlen($request->potential_products[0]) > 10) {
            return back()->with('error', 'Por favor, adicione mínimo un Producto Potencial.');
        } else {

            /** create temporal Customer */
            $customer = Customers::create($input);

            /** potential products array */
            foreach ($request->potential_products as $key => $value) {

                if (intval($request->qty[$key]) <= 0) {
                    Customers::find($customer->id)->delete();
                    return back()->with('error', 'Por favor, ingrese una cantidad válida en Producto Potencial.');
                } else {
                    /** Save potential product in table customer_parameters */
                    $item = new CustomerParameters();
                    $item->customer_id = $customer->id;
                    $item->potential_product_id = $request->potential_products[$key];
                    $item->quantity = $request->qty[$key];
                    $item->save();
                }
            }

            /** categories array */
            foreach ($request->category as $key => $value) {
                /** Save items in table customer_parameters */
                $item = new CustomerParameters();
                $item->customer_id = $customer->id;
                $item->category_id = $request->category[$key];
                $item->save();
            }
        }

        return redirect()->to('/user/customers')->with('message', 'Cliente registrado correctamente.');
    }

    public function show($id)
    {
        $customer = Customers::find($id);
        $idRefCurrentUser = Auth::user()->idReference;

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

        $potential_products_selectd = DB::table('customer_parameters')
            ->leftjoin('customers', 'customers.id', '=', 'customer_parameters.customer_id')
            ->leftjoin('parameters', 'parameters.id', '=', 'customer_parameters.potential_product_id')
            ->where('customers.id', '=', $id)
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->where('customer_parameters.potential_product_id', '!=', 0)
            ->select(
                'parameters.id',
                'parameters.name',
                'customer_parameters.quantity'
            )
            ->orderBy('parameters.created_at', 'DESC')
            ->get();

        return view('user::customers.show', compact('customer', 'categories', 'potential_products', 'potential_products_selectd', 'customerCategories', 'latitude', 'longitude'));
    }

    public function edit($id)
    {
        $customer = Customers::find($id);
        $idRefCurrentUser = Auth::user()->idReference;

        $potential_products_selectd = DB::table('customer_parameters')
            ->leftjoin('customers', 'customers.id', '=', 'customer_parameters.customer_id')
            ->leftjoin('parameters', 'parameters.id', '=', 'customer_parameters.potential_product_id')
            ->where('customers.id', '=', $id)
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->where('customer_parameters.potential_product_id', '!=', 0)
            ->select(
                'parameters.id',
                'parameters.name',
                'customer_parameters.quantity'
            )
            ->orderBy('parameters.created_at', 'DESC')
            ->get();

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

        return view('user::customers.edit', compact('customer', 'categories', 'potential_products_selectd', 'potential_products', 'customerCategories', 'customerPotentialProducts', 'estates', 'userEstate'));
    }

    public function update(Request $request, $id)
    {
        /** date validation, not less than 1980 and not greater than the current year **/
        $initialDate = '1980-01-01';
        $currentDate = (date('Y') + 2) . '-01-01'; //current year + 2 year 

        $request->validate([
            'name' => 'required|max:50|min:5',
            'phone' => 'required|max:25|min:5',
            'doc_id' => 'nullable|max:25|min:5|unique:customers,doc_id,' . $id,
            'email' => 'nullable|max:50|min:5|email:rfc,dns|unique:customers,email,' . $id,
            'address' => 'nullable|max:255|min:5',
            'city' => 'nullable|max:50|min:5',
            'estate' => 'required|max:50|min:5',
            'is_vigia' => 'nullable',
            'category' => 'required|max:150|min:1',
            'potential_products' => 'required|max:150|min:1',
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

        /** If not select any product */
        if (strlen($request->potential_products[0]) > 10) {
            return back()->with('error', 'Por favor, adicione mínimo un Producto Potencial.');
        } else {

            $customer = Customers::find($id);

            /** potential products array */
            foreach ($request->potential_products as $key => $value) {
                if (intval($request->qty[$key]) <= 0) {
                    return back()->with('error', 'Por favor, ingrese una cantidad válida en Producto Potencial.');
                } else {

                    // y si el id esta cadastrado pero ahora no encuentra es porque elimino del array. por ende tiene que eliminarse de la tabla

                    $potential_product_id = DB::table('customer_parameters')
                        ->where('customer_parameters.customer_id', '=', $customer->id)
                        ->where('customer_parameters.potential_product_id', '=', $request->potential_products[$key])
                        ->select(
                            'customer_parameters.potential_product_id',
                        )
                        ->first();

                    /** if find product id, update potential product; else create new potential product */
                    if ($potential_product_id) {
                        DB::table('customer_parameters')
                            ->where('customer_parameters.customer_id', '=', $customer->id)
                            ->where('customer_parameters.potential_product_id', '=', $request->potential_products[$key])
                            ->update([
                                'quantity' => $request->qty[$key],
                            ]);
                    } else {
                        $item = new CustomerParameters();
                        $item->customer_id = $customer->id;
                        $item->potential_product_id = $request->potential_products[$key];
                        $item->quantity = $request->qty[$key];
                        $item->save();
                    }
                }
            }

            /** categories array */
            foreach ($request->category as $key => $value) {

                $ifCatExist = DB::table('customer_parameters')
                    ->leftjoin('customers', 'customers.id', '=', 'customer_parameters.customer_id')
                    ->where('customer_parameters.category_id', '=', $request->category[$key])
                    ->select(
                        'customer_parameters.category_id',
                    )
                    ->first();

                /** if find category id, update category; */
                if ($ifCatExist) {
                    DB::table('customer_parameters')
                        ->where('customer_parameters.customer_id', '=', $customer->id)
                        ->where('customer_parameters.potential_product_id', '=', $request->potential_products[$key])
                        ->update([
                            'quantity' => $request->qty[$key],
                        ]);
                } else {
                    /** Save new category in table customer_parameters */
                    $item = new CustomerParameters();
                    $item->customer_id = $customer->id;
                    $item->category_id = $request->category[$key];
                    $item->save();
                }
            }

            $customer->update($input);
        }

        return redirect()->to('/user/customers')->with('message', 'Cliente actualizado correctamente.');
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

        $categories = DB::table('parameters')
            ->where('type', '=', 'Rubro')
            ->select('id', 'name')
            ->get();

        return view('user::customers.index', compact('customers', 'search', 'categories'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function destroy($id)
    {
        Customers::find($id)->delete();
        return redirect()->to('/user/customers')->with('message', 'Customer deleted successfully');
    }
}
