<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
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
    public function list(Request $request){
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
                    return $query->where('type',2)
                        ->whereNull('deleted_at');
                })
            ],
            'mobile' => 'required|string',
            'password' => 'required|confirmed|string|min:6',
            //'image' => 'mimes:jpg,jpeg,png|max:2048',
            'type' => 'required|numeric',
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
        //--- Validation Section Ends  ---//
        DB::beginTransaction();
        try {
            // Tenant Store
            $tenant = new Tenant();
            $tenant->type = $request->type;
            $tenant->name = $request->name;
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
            $user->status = 1;
            $user->type = 2; //landlord
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
     * Register api
     * @return \Illuminate\Http\Response
     */
}
