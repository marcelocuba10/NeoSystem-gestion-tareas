<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
//use Illuminate\Routing\Controller;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\User;

//spatie
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

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
            ->select('id', 'name', 'idReference', 'status', 'email','estate')
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
        $this->validate($request, [
            'name' => 'required|max:50|min:5',
            'seller_contact_1' => 'required|max:50|min:5',
            'seller_contact_2' => 'nullable|max:50|min:5',
            'phone_1' => 'nullable|max:25|min:5',
            'phone_2' => 'nullable|max:25|min:5',
            'status' => 'required|integer|between:0,1',
            'city' => 'nullable|max:50|min:5',
            'estate' => 'required|max:50|min:5',
            'address' => 'nullable|max:255|min:5',
            'email' => 'nullable|max:50|min:5|email:rfc,dns|unique:users,email',
            'password' => 'required|max:50|min:5',
            'confirm_password' => 'required|max:50|min:5|same:password',
            'doc_id' => 'required|max:25|min:5|unique:users,doc_id',
        ]);

        $input = $request->all();

        // generate idReference unique and random
        $input['idReference'] = $this->generateUniqueCode();
        $input['main_user'] = 1;

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

        return view('admin::sellers.show', compact('user', 'status','userEstate','estates'));
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
        $this->validate($request, [
            'name' => 'required|max:50|min:5',
            'seller_contact_1' => 'required|max:50|min:5',
            'seller_contact_2' => 'nullable|max:50|min:5',
            'phone_1' => 'nullable|max:25|min:5',
            'phone_2' => 'nullable|max:25|min:5',
            'status' => 'required|integer|between:0,1',
            'city' => 'nullable|max:50|min:5',
            'estate' => 'required|max:50|min:5',
            'address' => 'nullable|max:255|min:5',
            'email' => 'required|max:50|min:5|email:rfc,dns|unique:users,email,' . $id,
            'password' => 'nullable|max:50|min:5',
            'confirm_password' => 'nullable|max:20|min:5|same:password',
            'doc_id' => 'required|max:25|min:5|unique:users,doc_id,' . $id,
        ]);

        $input = $request->all();
        $input['main_user'] = 1;

        if (empty($input['password'])) {
            $input = Arr::except($input, array('password'));
        } else {
            if (empty($input['confirm_password'])) {
                return redirect()->to('/admin/sellers/edit/' . $id)->withErrors('Confirm password')->withInput();
            }
        }


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
