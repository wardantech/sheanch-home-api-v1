<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;


use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Settings\Facility;

class FacilityController extends Controller
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
    public function getList(Request $request)
    {
        $columns = ['id', 'name'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = Facility::select('*')->orderBy($columns[$column], $dir);

        $count = Facility::count();

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
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Store Utility
            $utility = new Facility();
            $utility->name = $request->name;
            $utility->description = $request->description;
            $utility->status = $request->status;
            $utility->created_by = Auth::user()->id;
            $utility->save();

            return $this->sendResponse(['id'=>$utility->id],'Facility create successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Facility store error', ['error' => $exception->getMessage()]);
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
            $category = Facility::findOrFail($id);

            return $this->sendResponse($category,'Facility data get successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Facility data error', ['error' => $exception->getMessage()]);
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
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Store Utility
            $utility = Facility::findOrFail($id);
            $utility->name = $request->name;
            $utility->description = $request->description;
            $utility->status = $request->status;
            $utility->updated_by = Auth::user()->id;
            $utility->update();

            return $this->sendResponse(['id'=>$utility->id],'Facility updated successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Facility updated error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Get all facilities
     */

    public function getFacilities()
    {
        try {
            $facility = Facility::where('status', 1)->get(['id','name']);
            return $this->sendResponse($facility, 'Facility list');

        } catch (\Exception $exception) {

            return $this->sendError('Facility list.', ['error' => $exception->getMessage()]);
        }
    }

    public function changeStatus(Request $request, $id)
    {
        try{
            $facility = Facility::findOrFail($id);

            if($request->status) {
                $facility->status = 0;
                $facility->update();
                return $this->sendResponse($facility,'Facility inactive successfully');
            }

            $facility->status = 1;
            $facility->update();
            return $this->sendResponse($facility,'Facility active successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Facility category status error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Facility Data Delete
     * @param $id
     * @return mixed
     */

    public function destroy($id)
    {
        try {
            $facility = Facility::findOrFail($id);
            $facility->delete();

            return $this->sendResponse(['id'=>$id],'Facility deleted successfully');
        }catch (\Exception $exception){
            return $this->sendError('Facility delete error', ['error' => $exception->getMessage()]);
        }
    }
}
