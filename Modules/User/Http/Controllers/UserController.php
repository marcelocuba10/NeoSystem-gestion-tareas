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
        $currentUser = Auth::user();
        $currentUserId = $currentUser->id;
        $idReference = $currentUser->idReference;

        $users = DB::table('users')
            ->where('idReference', '=', $idReference)
            ->select('id', 'name', 'idReference', 'email', 'status')
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        return view('user::users.index', compact('users', 'currentUserId'))->with('i', (request()->input('page', 1) - 1) * 10);
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

        $userRoleArray = $user->roles->pluck('name')->toArray(); //get user assigned role

        if (empty($userRoleArray)) {
            $userRole = null;
        } else {
            $userRole = $userRoleArray[0]; //get only name of the role
        }

        return view('user::users.editProfile', compact('user', 'roles', 'userRole', 'currentUserRole'));
    }

    public function updateProfile($id, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50|min:5',
            'last_name' => 'required|max:50|min:5',
            'email' => 'required|max:50|min:5|email:rfc,dns|unique:users,email,' . $id,
            'password' => 'nullable|max:50|min:5',
            'confirm_password' => 'nullable|max:50|min:5|same:password',
            'phone' => 'nullable|max:50|min:5',
            'doc_id' => 'nullable|max:25|min:5|unique:users,doc_id,' . $id,
            'address' => 'nullable|max:255|min:5',
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
