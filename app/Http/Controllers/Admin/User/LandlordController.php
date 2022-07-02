<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\Landlord;
use App\Models\Settings\FacilityCategory;
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
        $this->middleware(['auth:api']
            //['except' => ['imageUpload']]
        );
    }

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


    public function getlist(Request $request){

        $columns = ['id', 'name','mobile','status'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = DB::table('landlords')->where('deleted_at','=',null)
            ->select('id','name','status','mobile')

            ->orderBy($columns[$column], $dir);

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

        return ['data' => $fetchData, 'draw' => $request['params']['draw']];
    }

    /**
     * Register api
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request){

        //--- Validation Section Starts
        $rules = [
            'email' => [
                'required',
                Rule::unique('users')->where(function ($query) use ($request) {
//                    return $query->where('type',2)
//                        ->whereNull('deleted_at');
                    return $query->whereNull('deleted_at');

                })
            ],

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
            $landlord->email = $request->email;
            $landlord->mobile = $request->mobile;
            $landlord->status = $request->status;
            $landlord->nid = $request->nid;
            $landlord->thana_id = $request->thana_id;
            $landlord->district_id = $request->district_id;
            $landlord->division_id = $request->division_id;
            $landlord->postal_address = $request->postal_address;
            $landlord->residential_address = $request->residential_address;

            $landlord->save();

            $user = new User();
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->name = $request->name;
            $user->landlord_id = $landlord->id;
            $user->status = $request->status;
            $user->type = 2; //landlord
            $user->password = bcrypt($request->password);

            $user->save();

            DB::commit();

            return $this->sendResponse(['id'=>$landlord->id],'Landlord create successfully');

        }catch (\Exception $exception){
            DB::rollback();
            return $this->sendError('Landlord store error', ['error' => $exception->getMessage()]);
        }

    }

    public function show($id){
         try{
             $landlord = Landlord::findOrFail($id);

             return $this->sendResponse($landlord,'Landlord data get successfully');
         }
         catch (\Exception $exception){
             DB::rollback();
             return $this->sendError('Landlord data error', ['error' => $exception->getMessage()]);
         }
    }

    public function update(Request $request, $id){
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

    public function status(Request $request, $id)
    {
        try{
            $landlord = Landlord::findOrFail($id);
            $user = User::where('type', 2)->where('landlord_id',$id)->first();
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

}
