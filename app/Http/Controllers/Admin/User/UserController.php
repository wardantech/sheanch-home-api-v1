<?php

namespace App\Http\Controllers\Admin\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Models\Settings\Thana;
use App\Models\UserInformation;
use App\Service\PropertyService;
use App\Models\Settings\District;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserInformationResource;

class UserController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        $columns = ['id', 'name'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = User::select('*')
                ->where('is_admin', 0)
                ->orderBy($columns[$column], $dir);
        $count = User::count();

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

    public function create()
    {
        $divisions = PropertyService::getDivisions();

        return $this->sendResponse([
            'divisions' => $divisions
        ], 'User get successfully');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'name' => 'required|string|max:255',
            'mobile' => 'required|string',
            'thana_id' => 'required|numeric',
            'status' => 'nullable|numeric',
            'district_id' => 'required|numeric',
            'division_id' => 'required|numeric',
            'nid' => 'nullable|string',
            'postal_address' => 'nullable|string',
            'residential_address' => 'nullable|string',
            'password' => 'required|confirmed'
        ]);

        DB::beginTransaction();
        try {
            // Insert into users table
            $user = new User();
            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->email = $request->email;
            $user->status = $request->status;
            $user->password = bcrypt($request->password);
            $user->save();

            // Insert into user informations table
            $information = new UserInformation();

            $information->user_id = $user->id;
            $information->division_id = $request->division_id;
            $information->district_id = $request->district_id;
            $information->thana_id = $request->thana_id;
            $information->nid = $request->nid;
            $information->postal_address = $request->postal_address;
            $information->residential_address = $request->residential_address;
            $information->description = $request->description;
            $information->save();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'user' => $user,
                'type' => 'bearer'
            ]);
        }catch (\Exception $exception){
            DB::rollback();
            return $this->sendError('User create error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function show(Request $request)
    {
        $user = User::findOrFail($request->userId);

        return $this->sendResponse([
            'user' => new UserInformationResource($user)
        ], 'User get successfully');
    }

    public function edit(Request $request)
    {
        $user = User::findOrFail($request->userId);
        $divisions = PropertyService::getDivisions();
        $districts = District::where('division_id', $user->information->division_id)->get();
        $thanas = Thana::where('district_id', $user->information->district_id)->get();

        return $this->sendResponse([
            'divisions' => $divisions,
            'districts' => $districts,
            'thanas' => $thanas,
            'user' => new UserInformationResource($user)
        ], 'User get successfully');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'email' => 'required',
            'name' => 'required|string|max:255',
            'mobile' => 'required|string',
            'thana_id' => 'required|numeric',
            'status' => 'nullable|numeric',
            'district_id' => 'required|numeric',
            'division_id' => 'required|numeric',
            'nid' => 'nullable|string',
            'postal_address' => 'nullable|string',
            'residential_address' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Update into users table
            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->email = $request->email;
            $user->status = $request->status;
            $user->update();

            // Update into user informations table
            $user->information->user_id = $user->id;
            $user->information->division_id = $request->division_id;
            $user->information->district_id = $request->district_id;
            $user->information->thana_id = $request->thana_id;
            $user->information->nid = $request->nid;
            $user->information->postal_address = $request->postal_address;
            $user->information->residential_address = $request->residential_address;
            $user->information->description = $request->description;
            $user->information->update();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'User updated successfully',
                'user' => $user,
                'type' => 'bearer'
            ]);
        }catch (\Exception $exception){
            DB::rollback();
            return $this->sendError('User create error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            if ($request->status){
                $user->status = 0;
                $user->update();
            }else {
                $user->status = 1;
                $user->update();
            }

            return $this->sendResponse(['id' => $id], 'User status changed successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property status error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            UserInformation::where('user_id', $id)->first()->delete();
            User::findOrFail($id)->delete();

            DB::commit();
            return $this->sendResponse([ 'id'=> $id ], 'User deleted successfully');
        }catch (\Exception $exception){
            DB::rollback();
            return $this->sendError('User delete error', [
                'error' => $exception->getMessage()
            ]);
        }
    }

    public function image(Request $request, $id)
    {
        try{
            $imageName = uniqid('user-',false).'.'.$request->file->getClientOriginalExtension();
            $request->file->move(public_path('images'), $imageName);

            $information = UserInformation::where('user_id', $id)->first();
            $information->image = $imageName;
            $information->update();

            return response()->json([
                'success'=>'You have successfully upload file.'
            ]);
        }catch (\Exception $exception){
            return $this->sendError('User Image error', [
                'error' => $exception->getMessage()
            ]);
        }
    }
}
