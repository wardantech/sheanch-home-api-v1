<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;


use App\Models\Settings\UtilityCategory;
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
    public function list(Request $request)
    {
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
            'utility_category_id' => 'required',
            'description' => 'string'
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
            $utility->description = $request->description;
            $utility->status = $request->status;
            $utility->utility_category_id = $request->utility_category_id;
            $utility->created_by = Auth::user()->id;
            $utility->save();

            return $this->sendResponse(['id'=>$utility->id],'Utility create successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Utility store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Utility single data get for update or show
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        try{
            $category = Utility::findOrFail($id);

            return $this->sendResponse($category,'Utility data get successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Utility data error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        //--- Validation Section Start ---//
        $rules = [
            'name' => 'required|string|max:255',
            'utility_category_id' => 'required',
            'description' => 'string'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Store Utility
            $utility = Utility::findOrFail($id);
            $utility->name = $request->name;
            $utility->description = $request->description;
            $utility->status = $request->status;
            $utility->utility_category_id = $request->utility_category_id;
            $utility->updated_by = Auth::user()->id;
            $utility->update();

            return $this->sendResponse(['id'=>$utility->id],'Utility updated successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Utility updated error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Get all categories
     */

    public function getCategories()
    {
        try {
            $utilityCategories = UtilityCategory::where('status', true)->get();

            return $this->sendResponse($utilityCategories, 'Utility categories list');

        } catch (\Exception $exception) {

            return $this->sendError('Utility categories list.', ['error' => $exception->getMessage()]);
        }
    }
}
