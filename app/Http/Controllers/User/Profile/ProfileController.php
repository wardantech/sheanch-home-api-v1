<?php

namespace App\Http\Controllers\User\Profile;

use App\Http\Controllers\Controller;
use App\Models\Landlord;
use App\Models\Settings\District;
use App\Models\Settings\Division;
use App\Models\Settings\Thana;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get Landlord Data
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function getLandlordData(Request $request)
    {
        try{
            $landlord = Landlord::findOrFail($request->id);

            $divisions = Division::select('id', 'name')->get();
            $district = District::where('division_id', $landlord->division_id)->get();
            $thana = Thana::where('district_id', $landlord->district_id)->get();

            return $this->sendResponse([
                'landlord' => $landlord,
                'divisions' => $divisions,
                'districts' => $district,
                'thanas' => $thana,
            ], 'Get all data successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Landlord data error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * landlord update
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */

    public function landlordUpdate(Request $request, $id)
    {
        //--- Validation Section Starts
        $rules = [
            'email' => [
                'required',
                Rule::unique('users')->where(function ($query) use ($request, $id) {
                    return $query->whereNull('deleted_at')->where('id','==',$id);
                })
            ],

            'name' => 'required|string|max:255',
            'mobile' => 'required|string',
            'thana_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'division_id' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()),422);
        }
        //--- Validation Section Ends


        DB::beginTransaction();
        try {

            $landlord = Landlord::findOrFail($id);

            $landlord->name = $request->name;
            $landlord->email = $request->email;
            $landlord->mobile = $request->mobile;
            $landlord->status = $request->status;
            $landlord->nid = $request->nid;
            $landlord->thana_id = $request->thana_id;
            $landlord->district_id = $request->district_id;
            $landlord->division_id = $request->division_id;
            $landlord->postal_address = $request->postal_address;
            $landlord->residential_address = $request->residential_address;

            $landlord->update();

            $user = new User();
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->name = $request->name;
            $user->status = $request->status;

            $user->update();

            DB::commit();

            return $this->sendResponse(['id'=>$landlord->id],'Landlord update successfully');

        }catch (\Exception $exception){
            DB::rollback();
            return $this->sendError('Landlord update error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Image upload for landlord
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */

    public function imageUpload(Request $request, $id)
    {
        try{
            $imageName = uniqid('landlord-',false).'.'.$request->file->getClientOriginalExtension();
            $request->file->move(public_path('images'), $imageName);

            $landlord = Landlord::findOrFail($id);
            $landlord->image = $imageName;
            $landlord->update();

            return response()->json(['success'=>'You have successfully upload file.']);
        }
        catch (\Exception $exception){
            return $this->sendError('Landlord Image error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Tenant get all data
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

    public function getTenantData(Request $request)
    {
        try{
            $tenant = Tenant::findOrFail($request->id);

            $divisions = Division::select('id', 'name')->get();
            $district = District::where('division_id', $tenant->division_id)->get();
            $thana = Thana::where('district_id', $tenant->district_id)->get();

            return $this->sendResponse([
                'tenant' => $tenant,
                'divisions' => $divisions,
                'districts' => $district,
                'thanas' => $thana,
            ], 'Get all data successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Tenant data error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Tenant Update logic
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */

    public function TenantUpdate(Request $request, $id)
    {
        // Tenants Validation
        $rules = [
            'email' => [
                'required',
                Rule::unique('users')->where(function ($query) use ($request, $id) {
                    return $query->whereNull('deleted_at')->where('id','==',$id);
                })
            ],
            'mobile' => 'required|string',
            //'image' => 'mimes:jpg,jpeg,png|max:2048',
            'name' => 'required|string|max:255',
            'gender' => 'required|integer',
            'marital_status' => 'integer',
            'thana_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'division_id' => 'required|numeric',
            'postal_code' => 'string',
            'postal_address' => 'string',
            'physical_address' => 'string',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        // Tenants Validation End

        DB::beginTransaction();
        try {
            // Tenant Update
            $tenant = Tenant::findOrFail($id);

            $tenant->name = $request->name;
            $tenant->email = $request->email;
            $tenant->mobile = $request->mobile;
            $tenant->status = $request->status;
            $tenant->gender = $request->gender;
            $tenant->dob = $request->dob;
            $tenant->nid = $request->nid;
            $tenant->passport_no = $request->passport_no;
            $tenant->marital_status = $request->marital_status;
            $tenant->thana_id = $request->thana_id;
            $tenant->district_id = $request->district_id;
            $tenant->division_id = $request->division_id;
            $tenant->postal_code = $request->postal_code;
            $tenant->postal_address = $request->postal_address;
            $tenant->physical_address = $request->physical_address;
            $tenant->update();

            // User update
            $user = new User();
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->name = $request->name;
            $user->status = $request->status;
            $user->update();

            DB::commit();
            return $this->sendResponse(['id'=>$tenant->id],'Tenant updated successfully');

        }catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError('Tenant update error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Tenant image upload
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */

    public function tenantImageUpload(Request $request, $id)
    {
        try{
            $imageName = uniqid('tenant-',false).'.'.$request->file->getClientOriginalExtension();
            $request->file->move(public_path('images'), $imageName);

            $tenant = Tenant::findOrFail($id);
            $tenant->image = $imageName;
            $tenant->update();

            return response()->json(['success'=>'You have successfully upload file.']);
        }
        catch (\Exception $exception){
            return $this->sendError('Tenant Image error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * User password change logic
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */

    public function updatePassword(Request $request)
    {
        // Password Validation
        $rules = [
            'currentPassword' => 'required',
            'password' => 'required|confirmed'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        // Password Validation End

        try {
            $hashedPassword = Auth::user()->password;

            if (Hash::check($request->currentPassword, $hashedPassword)) {
                Auth::user()->update([
                    'password' => Hash::make($request->password)
                ]);

                return [
                    "status"=> true,
                    "message" => "Password change successfully"
                ];
            } else {
                return [
                    "status"=> false,
                    "message" => "Current password not match"
                ];
            }
        }catch (\Exception $exception){
            return $this->sendError('Password change error', ['error' => $exception->getMessage()]);
        }
    }
}
