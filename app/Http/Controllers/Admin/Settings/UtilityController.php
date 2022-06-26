<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;


use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Settings\Utility;

class UtilityController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        //$this->authRepository = $authRepository;
        //$this->middleware(['auth:api'], ['except' => ['login','register']]);
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

        $query = Utility::select('*')->orderBy($columns[$column], $dir);

        $count = Utility::count();

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
            'name' => 'required|string|max:255',
            'type' => 'required|integer',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Store Utility
            $utility = new Utility();
            $utility->name = $request->name;
            $utility->status = $request->status;
            $utility->type = $request->type;
            $utility->created_by = Auth::user()->id;
            $utility->save();

            return $this->sendResponse(['id'=>$utility->id],'Utility create successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Utility store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Register api
     * @return \Illuminate\Http\Response
     */


}
