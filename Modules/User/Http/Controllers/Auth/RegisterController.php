<?php

namespace Modules\User\Http\Controllers\Auth;

use Illuminate\Routing\Controller;
use Modules\User\Entities\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{

    public function show()
    {
        return view('user::auth.register');
    }

    public function register(Request $request)
    {
        // Check if the term box is checked
        if (empty($request['terms']) || $request['terms'] != 1) {
            $errors = ['terms' => 'Debes aceptar los términos y condiciones.'];
            return back()->withErrors($errors);
        }

        $request->validate(
            [
                'name' => 'required|max:50|min:2',
                'email' => 'required|max:50|min:5|email:rfc,dns|unique:users,email',
                'password' => 'required|max:50|min:2',
            ],
            [
                'password.required'  => 'El campo Contraseña es obligatorio.',
                'confirm_password.same'  => 'Las Contraseñas no coinciden',
                'name.required'  => 'El campo Nombre es obligatorio.',
                'name.min'  => 'El campo Nombre debe ser mayor a 1 dígito.',
                'email.required'  => 'El campo Email es obligatorio.',
                'email.unique'  => 'El Email ya esta en uso.',
            ]
        );

        $input = $request->all();
        
        $user = User::create($input);
        Auth()->login($user);

        return redirect()->to('/user/dashboard');
    }
}
