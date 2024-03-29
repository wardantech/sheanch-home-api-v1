<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $isAdmin = User::where('email', $request->email)->where('is_admin', 1)->first() ? true : false;

        if ($isAdmin) {
            $credentials = $request->only('email', 'password');
            $token = auth('api')->attempt($credentials);

            if (!$token) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                ], 401);
            }

            $user = auth('api')->user();

            return response()->json([
                'status' => 'success',
                'message' => 'Loged in successfully',
                'user' => $user,
                'access_token' => $token,
                'type' => 'bearer',
            ]);
        }else {
            return response([
                'error' => 'The Provided credentials are not correct'
            ], 422);
        }
    }

    public function register(Request $request)
    {
        //--- Validation Section
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|confirmed|string|min:6',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'status' => 0,
            'password' => Hash::make($request->password),
        ]);

        $token = auth('api')->login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'access_token' => $token,
            'type' => 'bearer',
        ]);
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function me()
    {
        $user = auth('api')->user();
        return response()->json($user);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }


}
