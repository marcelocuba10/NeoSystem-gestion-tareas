<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Routing\Controller;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\User;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:web', ['except' => ['logout']]);
    }

    public function index()
    {
        $currentUserId = Auth::id();

        $users = DB::table('users')
            ->where('users.status', '=', 1)
            ->select(
                'users.id',
                'users.name',
                'users.phone',
                'users.last_name',
                'users.status',
                'users.email',
            )
            ->orderBy('users.created_at', 'DESC')
            ->paginate(10);

        return view('user::users.index', compact('users', 'currentUserId'))->with('i', (request()->input('page', 1) - 1) * 10);
    }


    public function show($id)
    {
        $user = User::find($id);

        return view('user::users.show', compact('user'));
    }

    public function showProfile($id)
    {
        $user = User::find($id);

        return view('user::users.profile', compact('user'));
    }

    public function editProfile($id)
    {
        $user = User::find($id);

        return view('user::users.editProfile', compact('user'));
    }

    public function updateProfile($id, Request $request)
    {
        $request->validate(
            [
                'name' => 'required|max:50|min:2',
                'last_name' => 'required|max:50|min:2',
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'nullable|max:20|min:2',
                'doc_id' => 'nullable|max:25|min:2|unique:users,doc_id,' . $id,
                'password' => 'nullable|max:50|min:2',
                'confirm_password' => 'nullable|max:50|min:2|same:password',
            ],
            [
                'name.required'  => 'El campo Nombre es obligatorio.',
                'last_name.required'  => 'El campo Apellidos es obligatorio.',
                'email.required'  => 'El campo Email es obligatorio.',
                'email.unique'  => 'El Email ya esta en uso.',
                'doc_id.required'  => 'El campo Documento Identidad es obligatorio.',
                'doc_id.unique'  => 'El Documento Identidad ya esta en uso.',
                'doc_id.min'  => 'El Documento Identidad debe ser mayor a 1 dígito.',
            ]
        );

        $input = $request->all();

        if (empty($input['password'])) {
            $input = Arr::except($input, array('password'));
        } else {
            if (empty($input['confirm_password'])) {
                return redirect()->to('/user/users/edit/profile/' . $id)->withErrors('Confirme la Contraseña, déjelo en blanco si no desea cambiar la contraseña')->withInput();
            }
        }

        $user = User::find($id);
        $user->update($input);

        return redirect()->to('/user/users/profile/' . $id)->with('message', 'Registro actualizado correctamente');
    }

    public function updatePhotoProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate(
            [
                'photo' => 'image|mimes:jpeg,png,jpg|max:5048'
            ],
            [
                'photo.max'  => 'La imagen excede el límite permitido 2MB',
                'photo.mimes'  => 'La imagen debe ser en uno de los formatos:  jpg, jpeg, png',
                'photo.image'  => 'La imagen no es válida',
            ]
        );

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $photoName = str_replace(' ', '-', $file->getClientOriginalName());
            $file->move(public_path('/images/profiles/'), $photoName);

            // Update profile photo
            $user->img_profile = $photoName;
            $user->save();
        }

        return redirect()->to('user/users/profile/' . $user->id)->with('message', 'Foto de perfil actualizada correctamente');
    }

    public function search(Request $request)
    {
        $currentUser = Auth::user();
        $currentUserId = $currentUser->id;
        $search = $request->input('search');

        if ($search == '') {
            $users = DB::table('users')
                ->select(
                    'users.id',
                    'users.name',
                    'users.phone',
                    'users.last_name',
                    'users.status',
                    'users.email',
                )
                ->orderBy('users.created_at', 'DESC')
                ->paginate(10);
        } else {
            $users = DB::table('users')
                ->where('users.name', 'LIKE', "%{$search}%")
                ->select(
                    'users.id',
                    'users.name',
                    'users.phone',
                    'users.last_name',
                    'users.status',
                    'users.email',
                )
                ->orderBy('users.created_at', 'DESC')
                ->paginate();
        }

        return view('user::users.index', compact('users', 'search', 'currentUserId'))->with('i', (request()->input('page', 1) - 1) * 10);
    }
}
