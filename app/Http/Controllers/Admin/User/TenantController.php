<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;


class TenantController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        //$this->authRepository = $authRepository;
        //$this->middleware(['auth:api'], ['except' => ['login','register']]);
    }

    /**
     * Store api
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $request->toJson();
        if ($request->input('image')) {
            return $request->input('image');
            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('images'), $imageName);
        }
        return $request;
        $rules = [
            //'image' => 'mimes:jpg,jpeg,png|max:2048',
            'type' => 'required|numeric',
            'name' => 'required|string|max:255',
            'gender' => 'required|integer',
            'dob' => 'required|string',
            'nid' => 'string',
            'passport_no' => 'string',
            'marital_status' => 'integer',
            'thana_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'division_id' => 'required|numeric',
            'postal_code' => 'string',
            'postal_address' => 'string',
            'physical_address' => 'string',
        ];
        $validator = Validator::make($request->all(), $rules);
        //--- Validation Section Start ---//
        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//
        try {
            $tenant = new Tenant();
            $tenant->type = $request->type;
            $tenant->name = $request->name;
            $tenant->gender = $request->gender;
            $tenant->dob = $request->dob;
            $tenant->nid = $request->nid;
            $tenant->image = $imageName;
            $tenant->passport_no = $request->passport_no;
            $tenant->marital_status = $request->marital_status;
            $tenant->thana_id = $request->thana_id;
            $tenant->district_id = $request->district_id;
            $tenant->division_id = $request->division_id;
            $tenant->postal_code = $request->postal_code;
            $tenant->postal_address = $request->postal_address;
            $tenant->physical_address = $request->physical_address;
            $tenant->save();
        } catch (\Exception $exception) {
            return $this->sendError('Landlord store error', ['error' => $exception->getMessage()]);
        }

    }

    /**
     * Register api
     * @return \Illuminate\Http\Response
     */
}
