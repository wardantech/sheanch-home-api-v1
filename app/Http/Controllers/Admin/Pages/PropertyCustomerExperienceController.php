<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Controller;
use App\Models\Pages\PropertyCustomerExperience;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PropertyCustomerExperienceController extends Controller
{
    use ResponseTrait;

    /**
     * Get all Property Customer Experience lists
     * @param Request $request
     * @return array
     */

    public function getLists(Request $request)
    {
        $columns = ['id', 'video_link','status'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = DB::table('property_customer_experiences')->where('deleted_at','=',null)
            ->select('id', 'video_link','status')
            ->orderBy($columns[$column], $dir);

        $count = PropertyCustomerExperience::count();
        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('id', 'like', '%' . $searchValue . '%')
                    ->orWhere('video_link', 'like', '%' . $searchValue . '%');
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
     * Property Customer Experience Store
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(Request $request)
    {
        //--- Validation Section Starts
        $rules = [
            'video_link' => 'required',
            'status' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()),422);
        }
        //--- Validation Section Ends

        try {
            $customerExperience = new PropertyCustomerExperience();

            $customerExperience->video_link = $request->video_link;
            $customerExperience->status = $request->status;
            $customerExperience->save();

        }catch (\Exception $exception){
            return $this->sendError('Customer experience store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Faq status active inactive
     * @param Request $request
     * @param $id
     * @return mixed
     */

    public function changeStatus(Request $request, $id)
    {
        try{
            $customerExperience = PropertyCustomerExperience::findOrFail($id);
            if($request->status) {
                $customerExperience->status = 0;
                $customerExperience->update();

                return $this->sendResponse(['id'=>$id],'Customer experience inactive successfully');
            }

            $customerExperience->status = 1;
            $customerExperience->update();

            return $this->sendResponse(['id'=>$id],'Customer experience active successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Customer experience status error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Customer experience edit
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        try{
            $customerExperience = PropertyCustomerExperience::findOrFail($id);
            return $this->sendResponse($customerExperience,'Customer experience data get successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Customer experience data error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Customer experience Update
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        //--- Validation Section Starts
        $rules = [
            'video_link' => 'required',
            'status' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()),422);
        }
        //--- Validation Section Ends

        try {
            $customerExperience = PropertyCustomerExperience::findOrFail($id);

            $customerExperience->video_link = $request->video_link;
            $customerExperience->status = $request->status;
            $customerExperience->update();

        }catch (\Exception $exception){
            return $this->sendError('Customer experience store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Customer experience Delete
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        try {
            $customerExperience = PropertyCustomerExperience::findOrFail($id);
            $customerExperience->delete();

            return $this->sendResponse(['id'=>$id],'Customer experience deleted successfully');
        }catch (\Exception $exception){
            return $this->sendError('Customer experience delete error', ['error' => $exception->getMessage()]);
        }
    }
}
