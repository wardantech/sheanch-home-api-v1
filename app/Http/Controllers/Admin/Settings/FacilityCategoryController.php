<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\FacilityCategory;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacilityCategoryController extends Controller
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

        $query = FacilityCategory::select('*')->orderBy($columns[$column], $dir);

        $count = FacilityCategory::count();

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
     * Store Facility Category
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
            $category = new FacilityCategory();

            $category->name = $request->name;
            $category->status = $request->status;
            $category->description = $request->description;
            $category->created_by = Auth::user()->id;
            $category->save();

            return $this->sendResponse(['id'=>$category->id],'Facility category create successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Facility category store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Facility category single data get for update or show
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        try{
            $category = FacilityCategory::findOrFail($id);

            return $this->sendResponse($category,'Facility categories data get successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Facility categories data error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Update Facility Category
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
            // Store Facilities Category
            $category = FacilityCategory::findOrFail($id);

            $category->name = $request->name;
            $category->status = $request->status;
            $category->description = $request->description;
            $category->updated_by = Auth::user()->id;
            $category->update();

            return $this->sendResponse(['id'=>$category->id],'Facility category updated successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Facility category update error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * For Active | Inactive Utility categories
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function changeStatus(Request $request, $id)
    {
        try{
            $facilityCategory = FacilityCategory::findOrFail($id);

            if($request->status) {
                $facilityCategory->status = 0;
                $facilityCategory->update();
                return $this->sendResponse($facilityCategory,'Facility category inactive successfully');
            }

            $facilityCategory->status = 1;
            $facilityCategory->update();
            return $this->sendResponse($facilityCategory,'Facility category active successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Facility category status error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Facility Category Data Delete
     * @param $id
     * @return mixed
     */

    public function destroy($id)
    {
        try {
            $facilityCategory = FacilityCategory::findOrFail($id);
            $facilityCategory->delete();

            return $this->sendResponse(['id'=>$id],'Facility category deleted successfully');
        }catch (\Exception $exception){
            return $this->sendError('Facility category delete error', ['error' => $exception->getMessage()]);
        }
    }
}
