<?php

namespace App\Http\Controllers\Admin\Property;

use App\Http\Controllers\Controller;
use App\Models\Landlord;

use App\Models\Property\Property;
use App\Models\Property\PropertyAdsManager;
use App\Models\Settings\PropertyType;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PropertyAdsManagerController extends Controller
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

        $query = PropertyAdsManager::select('*')->orderBy($columns[$column], $dir);

        $count = PropertyAdsManager::count();

        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('id', 'like', '%' . $searchValue . '%')
                    ->orWhere('name', 'like', '%' . $searchValue . '%');
            });
        }

        if ($length != 'all') {
            $fetchData = $query->paginate($length);
        } else {
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
            'landlord_id' => 'required',
            'status' => 'required|integer',
            'security_money' => 'required',
            'start_date' => 'required',
            'property_type_id' => 'nullable|integer',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//


        try {
            // Store Property
            $PropertyAdsManager = new PropertyAdsManager();

            $PropertyAdsManager->landlord_id = $request->landlord_id;
            $PropertyAdsManager->property_type_id = $request->property_type_id;
            $PropertyAdsManager->rent_amount = $request->rent_amount;
            $PropertyAdsManager->security_money = $request->security_money;
            $PropertyAdsManager->description = $request->description;
            $PropertyAdsManager->start_date = $request->start_date;
            $PropertyAdsManager->status = $request->status;
            $PropertyAdsManager->created_by = Auth::id();
            $PropertyAdsManager->save();

            return $this->sendResponse(['id' => $PropertyAdsManager->id], 'Property create successfully');
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
        try {
            $PropertyAdsManager = PropertyAdsManager::findOrFail($id);

            return $this->sendResponse($PropertyAdsManager, 'Property data get successfully');
        } catch (\Exception $exception) {
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
            $PropertyAdsManager = PropertyAdsManager::findOrFail($id);

            $PropertyAdsManager->thana_id = $request->thana_id;
            $PropertyAdsManager->district_id = $request->district_id;
            $PropertyAdsManager->division_id = $request->division_id;
            $PropertyAdsManager->name = $request->name;
            $PropertyAdsManager->zip_code = $request->zip_code;
            $PropertyAdsManager->address = $request->address;
            $PropertyAdsManager->bed_rooms = $request->bed_rooms;
            $PropertyAdsManager->bath_rooms = $request->bath_rooms;
            $PropertyAdsManager->units = $request->units;
            $PropertyAdsManager->area_size = $request->area_size;
            $PropertyAdsManager->rent_amount = $request->rent_amount;
            $PropertyAdsManager->description = $request->description;
            $PropertyAdsManager->status = $request->status;
            $PropertyAdsManager->security_money = $request->security_money;
            $PropertyAdsManager->utilities_paid_by_landlord = $request->utilities_paid_by_landlord;
            $PropertyAdsManager->facilities_paid_by_landlord = $request->facilities_paid_by_landlord;
            $PropertyAdsManager->utilities_paid_by_tenant = $request->utilities_paid_by_tenant;
            $PropertyAdsManager->facilities_paid_by_tenant = $request->facilities_paid_by_tenant;
            $PropertyAdsManager->created_by = Auth::id();
            $PropertyAdsManager->update();

            return $this->sendResponse(['id' => $PropertyAdsManager->id], 'Property updated successfully');
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
        try {
            $PropertyAdsManager = Property::findOrFail($id);
            if ($request->status) {
                $PropertyAdsManager->status = 0;
                $PropertyAdsManager->update();

                return $this->sendResponse(['id' => $id], 'Property inactive successfully');
            }

            $PropertyAdsManager->status = 1;
            $PropertyAdsManager->update();

            return $this->sendResponse(['id' => $id], 'Property active successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property status error', ['error' => $exception->getMessage()]);
        }
    }

    public function getPropertyAsLandlord(Request $request)
    {
        try {
            $landlords = Property::where('landlord_id', $request->landlordId)
                ->where('status', true)->get();

            return $this->sendResponse($landlords, 'Landlord list');

        } catch (\Exception $exception) {

            return $this->sendError('Landlord list.', ['error' => $exception->getMessage()]);
        }
    }


}
