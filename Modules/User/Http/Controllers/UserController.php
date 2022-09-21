<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
//use Illuminate\Routing\Controller;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\Customers;
use Modules\User\Entities\User;

//spatie
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:web', ['except' => ['logout']]);

        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index']]);
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        return redirect()->to('user/dashboard');
    }

    public function showProfile($id)
    {
        $user = User::find($id);
        $roles = Role::where('guard_name', '=', 'web')
            ->where('name', '!=', 'Admin')
            ->pluck('name', 'name')
            ->all();

        $userRoleArray = $user->roles->pluck('name')->toArray(); //get user assigned role

        /** get current user role */
        $arrayCurrentUserRole = Auth::user()->roles->pluck('name');
        $currentUserRole = $arrayCurrentUserRole[0];

        return view('user::users.profile', compact('user', 'currentUserRole'));
    }

    public function editProfile($id)
    {
        /** get current user role */
        $arrayCurrentUserRole = Auth::user()->roles->pluck('name');
        $currentUserRole = $arrayCurrentUserRole[0];

        $user = User::find($id);
        $roles = Role::where('guard_name', '=', 'web')
            ->where('name', '!=', 'Admin')
            ->pluck('name', 'name')
            ->all();

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

        $userEstate = $user->estate;

        $userRoleArray = $user->roles->pluck('name')->toArray(); //get user assigned role

        return view('user::users.editProfile', compact('user', 'roles', 'currentUserRole','estates', 'userEstate'));
    }

    public function updateProfile($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50|min:5',
            'seller_contact_1' => 'required|max:50|min:5',
            'seller_contact_2' => 'nullable|max:50|min:5',
            'phone_1' => 'nullable|max:50|min:5',
            'phone_2' => 'nullable|max:50|min:5',
            'city' => 'nullable|max:50|min:5',
            'estate' => 'required|max:50|min:5',
            'address' => 'nullable|max:255|min:5',
            'email' => 'required|max:50|min:5|email:rfc,dns|unique:users,email,' . $id,
            'password' => 'nullable|max:50|min:5',
            'confirm_password' => 'nullable|max:50|min:5|same:password',
            'doc_id' => 'nullable|max:25|min:5|unique:users,doc_id,' . $id,
        ]);

        $input = $request->all();

        if (empty($input['password'])) {
            $input = Arr::except($input, array('password'));
        } else {
            if (empty($input['confirm_password'])) {
                return redirect()->to('user/users/edit/profile/' . $id)->withErrors('Confirm password')->withInput();
            }
        }

        $user = User::find($id);

        $user->update($input);

        /** Main user is Role Admin */
        $user->syncRoles('Admin');
        $user->assignRole('Admin');

        return redirect()->to('user/users/edit/profile/' . $id)->with('message', 'User Profile updated successfully');
    }
}
