<?php

namespace App\Http\Controllers\User\Auth;
use App\Http\Controllers\Controller;
use App\Models\Landlord;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ResponseTrait;
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function login(Request $request)
    {
        if(isset($request->token)){
            $user = auth('api')->user();
            return response()->json([
                'status' => 'success',
                'message' => 'Loged in successfully',
                'user' => $user,
                'access_token' => $request->token,
                'type' => 'bearer',
            ]);
        }

        $data = $this->validate($request, [
            'mobile' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('mobile',$data['mobile'])->first();
        if ($user->status == 0){
            return response()->json([
                'status' => false,
                'message' => 'Not Activated',
            ]);
        }

        $credentials = $request->only('mobile', 'password');

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

    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|unique:users',
            'password' => 'required|confirmed|string|min:6'
        ]);

        $user =  new User();
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->status = 0;
        $user->type = $request->type;
        $user->password = bcrypt($request->password);
        $user->save();

        //$credentials = $request->only('mobile', 'password');
        //$token = auth('api')->attempt($credentials);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            //'access_token' => $token,
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
