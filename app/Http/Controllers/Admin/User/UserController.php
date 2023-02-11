<?php

namespace App\Http\Controllers\Admin\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UserInformation;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;

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
            // $data['password'] = bcrypt($request->password);
            // $user = User::create($data);
            // $data['user_id'] = $user->id;

            // UserInformation::create($data);

            $user =  new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->status = $request->status;
            $user->password = bcrypt($request->password);
            $user->save();

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
                'type' => 'bearer',
            ]);
        }catch (\Exception $exception){
            DB::rollback();
            return $this->sendError('User create error', ['error' => $exception->getMessage()]);
        }
    }

    public function destroy($id)
    {
        return 50;
    }

    public function image(Request $request, $id)
    {
        try{
            $imageName = uniqid('tenant-',false).'.'.$request->file->getClientOriginalExtension();
            $request->file->move(public_path('images'), $imageName);

            $user = UserInformation::where('user_id', $id)->first();
            $user->image = $imageName;
            $user->update();

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
