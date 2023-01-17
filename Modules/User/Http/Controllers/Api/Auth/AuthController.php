<?php

namespace Modules\User\Http\Controllers\Api\Auth;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

//passport
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\User\Entities\User;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            //'remember_me' => 'boolean'
        ]);

        $credentials = $request->only(['email', 'password']);

        if (!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        // if ($request->remember_me)
        //     $token->expires_at = Carbon::now()->addWeeks(1);

        $token->save();

        return response()->json([
            'message' => 'Successfully logged...',
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:20|min:5',
            'last_name' => 'nullable|string|max:20|min:5',
            'phone' => 'nullable|string|max:20|min:5',
            'address' => 'nullable|string|max:100|min:5',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string',
        ]);

        // $input = $request->all();
        // $input['terms'] = 1; 
        // User::create($input);

        $user = new User;
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->status = 1;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->save();

        return response()->json([
            'message' => 'Successfully created user!'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /** Get the authenticated User **/
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function update(Request $request)
    {

        $input = $request->all();

        $request->validate([
            'name' => 'nullable|max:50|min:5',
            'seller_contact_1' => 'nullable|max:50|min:5',
            'seller_contact_2' => 'nullable|max:50|min:5',
            'phone_1' => 'nullable|max:50|min:5',
            'phone_2' => 'nullable|max:50|min:5',
            'city' => 'nullable|max:50|min:5',
            'estate' => 'nullable|max:50|min:5',
            'address' => 'nullable|max:255|min:5',
            'email' => 'nullable|max:50|min:5|email:rfc,dns|unique:users,email,' . $input['user']['id'],
            'password' => 'nullable|max:50|min:5',
            'confirm_password' => 'nullable|max:50|min:5|same:password',
            'doc_id' => 'nullable|max:25|min:5|unique:users,doc_id,' . $input['user']['id'],
        ]);

        if (empty($input['password'])) {
            $input = Arr::except($input, array('password'));
        } else {
            if (empty($input['confirm_password'])) {
                return response()->json([
                    'message' => 'Confirm password'
                ], 401);
            }
        }

        User::where('users.id', '=', $input['user']['id'])
            ->update([
                'latitude' => $input['user']['latitude'],
                'longitude' => $input['user']['longitude']
            ]);

        return response()->json([
            'message' => 'User updated',
            'data' => $input
        ]);
    }
}
