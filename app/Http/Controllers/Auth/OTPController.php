<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\OTPTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class OTPController extends Controller
{
    use OTPTrait, ResponseTrait;

    public function sendOTP(Request $request)
    {
        //--- Validation Section
        $rules = [
            'mobile' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()),422);
        }
        //--- Validation Section Ends

        try {
            $otp = random_int(1000, 9999);

            $text = "Thank you for sign up at sheanch-home. Your OTP is: ".$otp;
            $sms =  $this->sendSms($request->mobile, $text);

            if ($sms == true){
                return $this->sendResponse(['otp'=>$otp],'OTP send successfully');
            }else{
                return $this->sendError('OTP Send error', ['error' => $sms]);
            }

        }
        catch (\Exception $exception){
            return $this->sendError('OTP Send error', ['error' => $exception->getMessage()]);
        }

    }

}
