<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\Landlord;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Models\User;


class TenantController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        //$this->authRepository = $authRepository;
        //$this->middleware(['auth:api'], ['except' => ['login','register']]);
    }

    /**
     * Tenant Image Upload api
     * @return \Illuminate\Http\Response
     */
    public function imageUpload(Request $request, $id)
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
     * List api
     * @return \Illuminate\Http\Response
     */
    public function getList(Request $request){
        $columns = ['id', 'name'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = Tenant::select('*')->orderBy($columns[$column], $dir);

        $count = Tenant::count();

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('id', 'like', '%' . $searchValue . '%')
                    ->orWhere('name', 'like', '%' . $searchValue . '%');
            });
        }

        if($length!='all'){
            $fetchData = $query->paginate($length);
        }
        else{
            $fetchData = $query->paginate($count);
        }

        return ['data' => $fetchData, 'draw' => $request['params']['draw']];
    }

    /**
     * Store api
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //--- Validation Section Start ---//
        $rules = [
            'email' => [
                'required',
                Rule::unique('users')->where(function ($query) use ($request) {
                    return $query->whereNull('deleted_at');
                })
            ],
            'mobile' => 'required|string',
            'password' => 'required|confirmed|string|min:6',
            //'image' => 'mimes:jpg,jpeg,png|max:2048',
            'name' => 'required|string|max:255',
            'gender' => 'required|integer',
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
        //--- Validation Section Ends  ---//
        DB::beginTransaction();
        try {
            // Tenant Store
            $tenant = new Tenant();

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
            $tenant->save();

            // User Store
            $user = new User();
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->name = $request->name;
            $user->status = $request->status;
            $user->type = 3; //Tenant
            $user->password = bcrypt($request->password);
            $user->save();

            DB::commit();
            return $this->sendResponse(['id'=>$tenant->id],'Tenant create successfully');

        } catch (\Exception $exception) {
            DB::rollback();
            return $this->sendError('Tenant store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Show tenant single data for update
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        try{
            $landlord = Tenant::findOrFail($id);

            return $this->sendResponse($landlord,'Tenant data get successfully');
        }
        catch (\Exception $exception){
            DB::rollback();
            return $this->sendError('Tenant data error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * This method for tenant update
     * @param Request $request
     * @param $id
     */

    public function update(Request $request, $id)
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

    public function changeStatus(Request $request, $id)
    {
        try{
            $landlord = Tenant::findOrFail($id);
            $user = User::where('type', 3)->where('tenant_id',$id)->first();

            if($request->status) {
                $landlord->status = 0;
                $landlord->update();

                $user->status = 0;
                $user->update();

                return $this->sendResponse(['id'=>$id],'Landlord inactive successfully');
            }

            $landlord->status = 1;
            $landlord->update();

            $user->status = 1;
            $user->update();
            return $this->sendResponse(['id'=>$id],'Landlord active successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Landlord status error', ['error' => $exception->getMessage()]);
         }
   }

    /**
     * Tenant Data Delete
     * @param $id
     * @return mixed
     */

    public function destroy($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            $tenant->delete();

            return $this->sendResponse(['id'=>$id],'Tenant deleted successfully');
        }catch (\Exception $exception){
            return $this->sendError('Tenant delete error', ['error' => $exception->getMessage()]);
        }
    }
}
