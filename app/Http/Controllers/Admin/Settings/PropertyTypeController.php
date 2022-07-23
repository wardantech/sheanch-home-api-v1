<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\PropertyType;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class PropertyTypeController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        //$this->authRepository = $authRepository;
        //$this->middleware(['auth:api'], ['except' => ['login','register']]);
    }

    public function getList(Request $request)
    {
        $columns = ['id', 'name'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = PropertyType::select('*')->orderBy($columns[$column], $dir);

        $count = PropertyType::count();

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
     * Store Property Type
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        //--- Validation Section Start ---//
        $rules = [
            'name' => 'required|string|max:255',
            'status' => 'required',
            'description' => 'string|nullable'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Store Utilities Category
            $category = new PropertyType();

            $category->name = $request->name;
            $category->status = $request->status;
            $category->description = $request->description;
            $category->created_by = Auth::user()->id;
            $category->save();

            return $this->sendResponse(['id'=>$category->id],'Property Type create successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Property Type store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Property Type single data get for update or show
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        try{
            $category = PropertyType::findOrFail($id);

            return $this->sendResponse($category,'Property Type data get successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Property Type data error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Update Property Type
     * @param Request $request
     * @param $id
     */

    public function update(Request $request, $id)
    {
        //--- Validation Section Start ---//
        $rules = [
            'name' => 'required|string|max:255',
            'status' => 'required',
            'description' => 'string|nullable'
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Store Utilities Category
            $category = PropertyType::findOrFail($id);

            $category->name = $request->name;
            $category->status = $request->status;
            $category->description = $request->description;
            $category->updated_by = Auth::user()->id;
            $category->update();

            return $this->sendResponse(['id'=>$category->id],'Property Type updated successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Property Type update error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * For Active | Inactive Property Type
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function status(Request $request, $id)
    {
        try{
            $propertyType = PropertyType::findOrFail($id);

            if($request->status) {
                $propertyType->status = 0;
                $propertyType->update();
                return $this->sendResponse($propertyType,'Property Type inactive successfully');
            }

            $propertyType->status = 1;
            $propertyType->update();
            return $this->sendResponse($propertyType,'Property Type active successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Property Type status error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Property Type Data Delete
     * @param $id
     * @return mixed
     */

    public function destroy($id)
    {
        try {
            $propertyType = PropertyType::findOrFail($id);
            $propertyType->delete();

            return $this->sendResponse(['id'=>$id],'Property type deleted successfully');
        }catch (\Exception $exception){
            return $this->sendError('Property type delete error', ['error' => $exception->getMessage()]);
        }
    }
}
