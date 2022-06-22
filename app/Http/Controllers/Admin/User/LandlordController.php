<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\Landlord;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;


class LandlordController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        //$this->authRepository = $authRepository;
        $this->middleware(['auth:api'],['except' => ['formSubmit']]);
    }

    public function formSubmit(Request $request)
    {

        $imageName = time().'.'.$request->file->getClientOriginalExtension();
        $request->file->move(public_path('images'), $imageName);

        return response()->json(['success'=>'You have successfully upload file.']);
    }

    public function list(Request $request){

        $columns = ['id', 'name'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = Landlord::select('*')->orderBy($columns[$column], $dir);

        $count = Landlord::count();
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

        //$footer = $fetchData -> sum('id');

        return ['data' => $fetchData, 'draw' => $request['params']['draw']];
    }

    /**
     * Register api
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request){
        return $request->toJson();
        if($request->input('image')) {
            return $request->input('image');
            $imageName = time().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('images'), $imageName);
        }
        return $request;
        $rules = [
            'email' => [
                'required',
                Rule::unique('users')->where(function ($query) use ($request) {
                    return $query->where('type',2)
                        ->whereNull('deleted_at');
                })
            ],
            //'image' => 'mimes:jpg,jpeg,png|max:2048',
            'name' => 'required|string|max:255',
            'mobile' => 'required|string',
            'thana_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'division_id' => 'required|numeric',
            'password' => 'required|confirmed|string|min:6',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()),422);
        }
        //--- Validation Section Ends


        DB::beginTransaction();
        try {

            $landlord = new Landlord();

            $landlord->name = $request->name;
            $landlord->nid = $request->nid;
            $landlord->thana_id = $request->thana_id;
            $landlord->district_id = $request->district_id;
            $landlord->division_id = $request->division_id;
            $landlord->postal_address = $request->postal_address;
            $landlord->residential_address = $request->residential_address;


            $landlord->image = $imageName;
            $landlord->save();

            $user = new User();
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->name = $request->name;
            $user->status = 1;
            $user->type = 2; //landlord
            $user->password = bcrypt($request->password);

            $user->save();

            DB::commit();

        }catch (\Exception $exception){
            DB::rollback();
            return $this->sendError('Landlord store error', ['error' => $exception->getMessage()]);
        }

    }

}
