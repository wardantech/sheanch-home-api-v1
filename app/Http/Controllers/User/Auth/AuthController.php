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
        //--- Validation Section
        $rules = [
            'mobile' => 'required|string',
            'password' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()),422);
        }
        //--- Validation Section Ends

        $user = User::where('mobile',$request->mobile)->first();
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
        //--- Validation Section
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|integer',
            'mobile' => 'required|string|unique:users',
            'password' => 'required|confirmed|string|min:6',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends

        $user =  new User();
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->status = 0;
        $user->type = $request->type;
        $user->password = bcrypt($request->password);

        if($request->type == 2){
            $landlord = new Landlord();

            $landlord->name = $request->name;
            $landlord->mobile = $request->mobile;
            $landlord->status = 0;

            $landlord->save();

            $user->landlord_id = $landlord->id;
        }

        if($request->type == 3){
            $tenant = new Tenant();

            $tenant->name = $request->name;
            $tenant->mobile = $request->mobile;
            $tenant->status = 0;

            $tenant->save();

            $user->landlord_id = $tenant->id;

        }

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
