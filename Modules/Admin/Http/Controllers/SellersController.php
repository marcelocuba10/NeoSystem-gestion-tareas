<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
//use Illuminate\Routing\Controller;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\User;

class SellersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['logout']]);

        $this->middleware('permission:seller-sa-list|seller-sa-create|seller-sa-edit|seller-sa-delete', ['only' => ['index']]);
        $this->middleware('permission:seller-sa-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:seller-sa-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:seller-sa-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $users = DB::table('users')
            ->where('main_user', '=', 1)
            ->select(
                'id',
                'idReference',
                'name',
                'status',
                'phone_1',
                'city',
                'estate'
            )
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        return view('admin::sellers.index', compact('users'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        /** get current user role */
        $arrayCurrentUserRole = Auth::user()->roles->pluck('name');
        $currentUserRole = $arrayCurrentUserRole[0];

        $status = array(
            array('1', 'Habilitado'),
            array('0', 'Inhabilitado'),
        );

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

        $user = null;
        $userStatus = null;
        $userEstate = null;

        return view('admin::sellers.create', compact('user', 'currentUserRole', 'userStatus', 'status', 'userEstate', 'estates'));
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|max:50|min:2',
                'seller_contact_1' => 'nullable|max:50|min:2',
                'seller_contact_2' => 'nullable|max:50|min:2',
                'meta_visits' => 'nullable|max:999|min:1',
                'meta_billing' => 'nullable|max:12|min:1',
                'phone_1' => 'nullable|max:50|min:2',
                'phone_2' => 'nullable|max:50|min:2',
                'status' => 'required|integer|between:0,1',
                'city' => 'nullable|max:50|min:2',
                'estate' => 'nullable|max:50|min:2',
                'address' => 'nullable|max:255|min:2',
                'email' => 'required|max:50|min:5|email:rfc,dns|unique:users,email',
                'password' => 'required|max:50|min:2',
                'confirm_password' => 'required|max:50|min:2|same:password',
                'doc_id' => 'required|max:25|min:2|unique:users,doc_id',
            ],
            [
                'doc_id.required'  => 'El campo Documento Identidad es obligatorio.',
                'password.required'  => 'El campo Contraseña es obligatorio.',
                'confirm_password.required'  => 'El campo Confirmar Contraseña es obligatorio.',
                'confirm_password.same'  => 'Las Contraseñas no coinciden',
                'name.required'  => 'El campo Nombre es obligatorio.',
                'name.min'  => 'El campo Nombre debe ser mayor a 1 dígito.',
                'phone_1.min'  => 'El campo Teléfono 1 debe ser mayor a 1 dígito.',
                'phone_2.min'  => 'El campo Teléfono 2 debe ser mayor a 1 dígito.',
                'address.min'  => 'El campo Dirección debe ser mayor a 1 dígito.',
                'city.min'  => 'El campo Ciudad debe ser mayor a 1 dígito.',
                'seller_contact_1.min'  => 'El campo Nombre del Encargado debe ser mayor a 1 dígito.',
                'email.required'  => 'El campo Email es obligatorio.',
                'doc_id.unique'  => 'El Documento Identidad ya esta en uso.',
                'doc_id.min'  => 'El Documento Identidad debe ser mayor a 1 dígito.',
                'email.unique'  => 'El Email ya esta en uso.',
            ]
        );

        $input = $request->all();

        // generate idReference unique and random
        $input['idReference'] = $this->generateUniqueCode();
        $input['main_user'] = 1;

        //remove the separator thousands, example: 1.000.000 to 1000000
        $input['meta_billing'] = str_replace('.', '', $input['meta_billing']);

        $user = User::create($input);
        /** Main user is Role Admin */
        $user->assignRole('Admin');

        return redirect()->to('/admin/sellers')->with('message', 'User created successfully.');
    }

    public function generateUniqueCode()
    {
        do {
            $idReference = random_int(100000, 999999);
        } while (
            DB::table('users')->where("idReference", "=", $idReference)->first()
        );

        return $idReference;
    }

    public function show($id)
    {
        $user = User::find($id);

        $status = array(
            array('1', 'Habilitado'),
            array('0', 'Inhabilitado'),
        );

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

        $userStatus = $user->status;
        $userEstate = $user->estate;

        /** Get visits by agent */
        $customer_visits = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.seller_id', '=', $user->idReference)
            ->select(
                'customer_visits.id',
                'customer_visits.visit_number',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.status',
                'customer_visits.type',
                'customer_visits.action',
                'customers.name AS customer_name',
                'customers.estate'
            )
            ->orderBy('customer_visits.created_at', 'DESC')
            ->paginate(20);

        return view('admin::sellers.show', compact('customer_visits', 'user', 'status', 'userEstate', 'estates'));
    }

    public function edit($id)
    {
        /** get current user role */
        $arrayCurrentUserRole = Auth::user()->roles->pluck('name');
        $currentUserRole = $arrayCurrentUserRole[0];

        $status = array(
            array('1', 'Habilitado'),
            array('0', 'Inhabilitado'),
        );

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

        $user = User::find($id);

        $userStatus = $user->status;
        $userEstate = $user->estate;

        return view('admin::sellers.edit', compact('user', 'currentUserRole', 'userStatus', 'status', 'estates', 'userEstate'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'name' => 'required|max:50|min:2',
                'seller_contact_1' => 'nullable|max:50|min:2',
                'seller_contact_2' => 'nullable|max:50|min:2',
                'meta_visits' => 'nullable|max:999|min:1',
                'meta_billing' => 'nullable|max:12|min:1',
                'phone_1' => 'nullable|max:25|min:2',
                'phone_2' => 'nullable|max:25|min:2',
                'status' => 'required|integer|between:0,1',
                'city' => 'nullable|max:50|min:2',
                'estate' => 'nullable|max:50|min:2',
                'address' => 'nullable|max:255|min:2',
                'email' => 'required|max:50|min:2|email:rfc,dns|unique:users,email,' . $id,
                'password' => 'nullable|max:50|min:2',
                'confirm_password' => 'nullable|max:20|min:2|same:password',
                'doc_id' => 'required|max:25|min:2|unique:users,doc_id,' . $id,
            ],
            [
                'doc_id.required'  => 'El campo Documento Identidad es obligatorio.',
                'password.required'  => 'El campo Contraseña es obligatorio.',
                'confirm_password.required'  => 'El campo Confirmar Contraseña es obligatorio.',
                'confirm_password.same'  => 'Las Contraseñas no coinciden',
                'name.required'  => 'El campo Nombre es obligatorio.',
                'name.min'  => 'El campo Nombre debe ser mayor a 1 dígito.',
                'phone_1.min'  => 'El campo Teléfono 1 debe ser mayor a 1 dígito.',
                'phone_2.min'  => 'El campo Teléfono 2 debe ser mayor a 1 dígito.',
                'address.min'  => 'El campo Dirección debe ser mayor a 1 dígito.',
                'city.min'  => 'El campo Ciudad debe ser mayor a 1 dígito.',
                'email.required'  => 'El campo Email es obligatorio.',
                'doc_id.unique'  => 'El Documento Identidad ya esta en uso.',
                'doc_id.min'  => 'El Documento Identidad debe ser mayor a 1 dígito.',
                'email.unique'  => 'El Email ya esta en uso.',
            ]
        );

        $input = $request->all();
        $input['main_user'] = 1;

        if (empty($input['password'])) {
            $input = Arr::except($input, array('password'));
        } else {
            if (empty($input['confirm_password'])) {
                return redirect()->to('/admin/sellers/edit/' . $id)->withErrors('Confirm password')->withInput();
            }
        }

        //remove the separator thousands, example: 1.000.000 to 1000000
        $input['meta_billing'] = str_replace('.', '', $input['meta_billing']);

        $user = User::find($id);
        $user->update($input);

        /** Main user is Role Admin */
        $user->assignRole('Admin');

        return redirect()->to('/admin/sellers')->with('message', 'Registro actualizado correctamente');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        if ($search == '') {
            $users = DB::table('users')
                ->orderBy('created_at', 'DESC')
                ->paginate(10);
        } else {
            $users = DB::table('users')
                ->where('users.name', 'LIKE', "%{$search}%")
                ->orderBy('created_at', 'DESC')
                ->paginate();
        }

        return view('admin::sellers.index', compact('users', 'search'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->to('/admin/sellers')->with('message', 'Agent deleted successfully');
    }
}
