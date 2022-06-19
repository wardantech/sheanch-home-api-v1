<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    use ResponseTrait;

//    public function __construct()
//    {
//        $this->middleware(['auth:api']);
//    }

    public function index(){
        return 40;
    }

    /**
     * Store api
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        try {
            $user = new User();

            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile ?? '';
            $user->type = 1; //admin
            $user->password = bcrypt($request->password);

            $user->save();

            return $this->sendResponse($user, 'User created successfully.');

        } catch (\Exception $exception) {
            return $this->sendError('Not created', ['error' => $exception->getMessage()]);
        }
    }


}
