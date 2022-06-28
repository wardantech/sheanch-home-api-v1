<?php

namespace App\Http\Controllers\User\Auth;
use App\Http\Controllers\Controller;
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
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()),422);
        }
        //--- Validation Section Ends

        $user = User::where('email',$request->email)->first();
        if ($user->status == 0){
            return response()->json([
                'status' => false,
                'message' => 'Not Activated',
            ]);
        }

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

    }

    public function register(Request $request){

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

    // otp
    public function otp(Request $request)
    {
        //--- Validation Section
        $rules = [
            'mobile' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        //--- OTP Section
        $otp = mt_rand(1000, 9999);
        $phone = $request->phone;
        try {
            $smsUrl = "http://gosms.xyz/api/v1/sendSms?username=medylife&password=Vu3wq8e7j7KqqQN&number=(" . $phone . ")&sms_content=Your%20OTP%20is:%20" . $otp . "&sms_type=1&masking=non-masking";

            //otp table
            $otp_code = new Otp();
            $otp_code->name = $request->name;
            $otp_code->password = $request->password;
            $otp_code->email = $request->email;
            $otp_code->phone = $request->phone;
            $otp_code->otp = $otp;
            $otp_code->save();

            //--- Send api sms request

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $smsUrl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POST, false);
            curl_exec($curl); //response output
            curl_close($curl);

            return response()->json(['status' => 'success','message'=>'Otp sent successfully'], 200);

        } catch (\Exception $exception) {

            return response()->json($exception->getMessage());

        }
    }


}
