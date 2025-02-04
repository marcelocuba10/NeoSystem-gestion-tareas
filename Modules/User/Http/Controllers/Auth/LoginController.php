<?php

namespace Modules\User\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\User\Entities\User;

class LoginController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest', ['except' => ['logout']]);
    }

    public function show()
    {
        return view('user::auth.login');
    }

    public function login(Request $request)
    {

        $credentials = $request->validate([
            'email' => 'required|email:rfc,dns|min:6|max:100',
            'password' => 'required|min:6|max:50'
        ],
        [
            'email.email'    => 'Correo electrónico inválido.'
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        /** Validations */
        $user = User::where('email', '=', $email)->first();

        if (!$user) {
            return redirect()->to('/user/login')->with('error', 'Correo electrónico no encontrado.');
        }

        if (!Hash::check($password, $user->password)) {
            return redirect()->to('/user/login')->with('error', 'Contraseña incorrecta.');
        }

        if (!Auth::validate($credentials)) {
            return redirect()->to('/user/login')->with('error', 'Credenciales incorrectas');
        }

        /** Check if user is enabled or disabled */
        if ($user->status == 0) {
            return redirect()->to('/user/login')->with('error', 'Usuario inhabilitado');
        } else {
            $user = Auth::getProvider()->retrieveByCredentials($credentials);
            // Auth::login($user);
            Auth::login($user, $request->get('remember'));
            return redirect()->to('/user/dashboard');
        }
    }
}
