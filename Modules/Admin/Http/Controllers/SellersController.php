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
            ->select('id', 'name', 'idReference', 'status', 'email')
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

        $user = null;
        $userStatus = null;

        return view('admin::sellers.create', compact('user', 'currentUserRole', 'userStatus', 'status'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'status' => 'required|integer|between:0,1',
            'name' => 'required|max:50|min:5',
            'last_name' => 'required|max:50|min:5',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|max:20|min:5',
            'doc_id' => 'required|max:25|min:5|unique:users,doc_id',
            'password' => 'required|max:50|min:5',
            'confirm_password' => 'required|max:50|min:5|same:password',
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
            array('0', 'Inhabilitado')
        );

        return view('admin::sellers.show', compact('user', 'status'));
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

        $user = User::find($id);
        $userStatus = $user->status;

        return view('admin::sellers.edit', compact('user', 'currentUserRole', 'userStatus','status'));
    }

    public function update(Request $request, $id)
    {
        dd();
        $this->validate($request, [
            'status' => 'required|integer|between:0,1',
            'name' => 'required|max:50|min:5',
            'last_name' => 'required|max:50|min:5',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|max:50|min:5',
            'doc_id' => 'required|max:25|min:5|unique:users,doc_id,' . $id,
            'password' => 'nullable|max:50|min:5',
            'confirm_password' => 'nullable|max:20|min:5|same:password',
        ]);

        $input = $request->all();
        $input['roles'] = 'Admin';
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

        return redirect()->to('/admin/sellers')->with('message', 'Registro actualizado correctamente');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        if ($search == '') {
            $users = DB::table('users')->paginate(10);
        } else {
            $users = DB::table('users')
                ->where('users.name', 'LIKE', "%{$search}%")
                ->paginate();
        }

        return view('admin::sellers.index', compact('users', 'search'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->to('/admin/sellers')->with('message', 'User deleted successfully');
    }
}
