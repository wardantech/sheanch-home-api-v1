<?php

namespace App\Http\Controllers\Admin\Property;

use App\Http\Controllers\Controller;
use App\Models\Property\Lease;
use App\Models\Property\Property;
use App\Models\Settings\PropertyType;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LeaseController extends Controller
{
    use ResponseTrait;

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

        $query = Lease::select('*')->orderBy($columns[$column], $dir);

        $count = Lease::count();

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
            'thana_id' => 'required',
            'district_id' => 'required',
            'division_id' => 'required',
            'name' => 'required|string|max:255',
            'zip_code' => 'required|string|max:255',
            'address' => 'required|string',
            'bed_rooms' => 'integer|nullable',
            'bath_rooms' => 'integer|nullable',
            'units' => 'integer|nullable',
            'area_size' => 'integer|nullable',
            'rent_amount' => 'required',
            'status' => 'required|integer',
            'security_money' => 'required',
            'property_type_id' => 'nullable|integer',
            'landlord_id' => 'nullable|integer',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//



        try {
            // Store Property
            $lease = new Lease();

            $lease->thana_id = $request->thana_id;
            $lease->district_id = $request->district_id;
            $lease->division_id = $request->division_id;
            $lease->property_type_id = $request->property_type_id;
            $lease->landlord_id = $request->landlord_id;
            $lease->name = $request->name;
            $lease->zip_code = $request->zip_code;
            $lease->address = $request->address;
            $lease->bed_rooms = $request->bed_rooms;
            $lease->bath_rooms = $request->bath_rooms;
            $lease->units = $request->units;
            $lease->area_size = $request->area_size;
            $lease->rent_amount = $request->rent_amount;
            $lease->description = $request->description;
            $lease->status = $request->status;
            $lease->security_money = $request->security_money;
            $lease->utilities_paid_by_landlord = json_encode($request->utilities_paid_by_landlord);
            $lease->utilities_paid_by_tenant = json_encode($request->utilities_paid_by_tenant);
            $lease->facilities = json_encode($request->facilities);
            $lease->created_by = Auth::id();
            $lease->save();

            return $this->sendResponse(['id'=>$lease->id],'Property create successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property store error', ['error' => $exception->getMessage()]);
        }
    }
    /**
     * Property single data get for update or show
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        try{
            $lease = Lease::findOrFail($id);

            return $this->sendResponse($lease,'Property data get successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Property data error', ['error' => $exception->getMessage()]);
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
            'thana_id' => 'required',
            'district_id' => 'required',
            'division_id' => 'required',
            'name' => 'required|string|max:255',
            'zip_code' => 'required|string|max:255',
            'address' => 'required|string',
            'bed_rooms' => 'integer|nullable',
            'bath_rooms' => 'integer|nullable',
            'units' => 'integer|nullable',
            'area_size' => 'integer|nullable',
            'rent_amount' => 'required',
            'description' => 'string|nullable',
            'status' => 'integer|nullable',
            'security_money' => 'required',
            'utilities_paid_by_landlord' => 'nullable|string',
            'facilities_paid_by_landlord' => 'nullable|string',
            'utilities_paid_by_tenant' => 'nullable|string',
            'facilities_paid_by_tenant' => 'nullable|string',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Update Property
            $lease = Lease::findOrFail($id);

            $lease->thana_id = $request->thana_id;
            $lease->district_id = $request->district_id;
            $lease->division_id = $request->division_id;
            $lease->name = $request->name;
            $lease->zip_code = $request->zip_code;
            $lease->address = $request->address;
            $lease->bed_rooms = $request->bed_rooms;
            $lease->bath_rooms = $request->bath_rooms;
            $lease->units = $request->units;
            $lease->area_size = $request->area_size;
            $lease->rent_amount = $request->rent_amount;
            $lease->description = $request->description;
            $lease->status = $request->status;
            $lease->security_money = $request->security_money;
            $lease->utilities_paid_by_landlord = $request->utilities_paid_by_landlord;
            $lease->facilities_paid_by_landlord = $request->facilities_paid_by_landlord;
            $lease->utilities_paid_by_tenant = $request->utilities_paid_by_tenant;
            $lease->facilities_paid_by_tenant = $request->facilities_paid_by_tenant;
            $lease->created_by = Auth::id();
            $lease->update();

            return $this->sendResponse(['id'=>$lease->id],'Property updated successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property updated error', ['error' => $exception->getMessage()]);
        }
    }


    /**
     * Status Active or Inactive
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function status(Request $request, $id)
    {
        try{
            $lease = Property::findOrFail($id);
            if($request->status) {
                $lease->status = 0;
                $lease->update();

                return $this->sendResponse(['id'=>$id],'Property inactive successfully');
            }

            $lease->status = 1;
            $lease->update();

            return $this->sendResponse(['id'=>$id],'Property active successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Property status error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Image upload for landlord
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */


}
