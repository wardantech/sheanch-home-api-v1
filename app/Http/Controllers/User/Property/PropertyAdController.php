<?php

namespace App\Http\Controllers\User\Property;

use App\Http\Controllers\Controller;
use App\Models\Landlord;

use App\Models\Property\Property;
use App\Models\Property\PropertyAd;
use App\Models\Settings\PropertyType;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PropertyAdController extends Controller
{
    use ResponseTrait;
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getActivePropertyList']]);
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

        $query = PropertyAd::select('*')->with(['landlord','property'])
            ->where('landlord_id', Auth::user()->landlord_id)
            ->orderBy($columns[$column], $dir);

        $count = PropertyAd::count();

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
        //return $request->input();
        //--- Validation Section Start ---//
        $rules = [
            'landlord_id' => 'required',
            'security_money' => 'required',
            'start_date' => 'required',
            'property_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//


        try {
            // Store Property
            $PropertyAd = new PropertyAd();

            $PropertyAd->landlord_id = $request->landlord_id;
            $PropertyAd->property_id = $request->property_id;
            $PropertyAd->property_category = $request->property_category_id;
            $PropertyAd->property_type_id = $request->property_type_id;
            $PropertyAd->sale_type = $request->sale_type;
            $PropertyAd->division_id = $request->division_id;
            $PropertyAd->district_id = $request->district_id;
            $PropertyAd->thana_id = $request->thana_id;
            $PropertyAd->rent_amount = $request->rent_amount;
            $PropertyAd->security_money = $request->security_money;
            $PropertyAd->description = $request->description;
            $PropertyAd->start_date = $request->start_date;
            $PropertyAd->end_date = $request->end_date;
            $PropertyAd->status = 0;
            $PropertyAd->created_by = Auth::id();
            $PropertyAd->save();

            return $this->sendResponse(['id' => $PropertyAd->id], 'Property Ad create successfully');
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
            $PropertyAd = PropertyAd::findOrFail($id);

            return $this->sendResponse($PropertyAd, 'Property data get successfully');
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
            $PropertyAd = PropertyAd::findOrFail($id);

            $PropertyAd->thana_id = $request->thana_id;
            $PropertyAd->district_id = $request->district_id;
            $PropertyAd->division_id = $request->division_id;
            $PropertyAd->name = $request->name;
            $PropertyAd->zip_code = $request->zip_code;
            $PropertyAd->address = $request->address;
            $PropertyAd->bed_rooms = $request->bed_rooms;
            $PropertyAd->bath_rooms = $request->bath_rooms;
            $PropertyAd->units = $request->units;
            $PropertyAd->area_size = $request->area_size;
            $PropertyAd->rent_amount = $request->rent_amount;
            $PropertyAd->description = $request->description;
            $PropertyAd->status = $request->status;
            $PropertyAd->security_money = $request->security_money;
            $PropertyAd->utilities_paid_by_landlord = $request->utilities_paid_by_landlord;
            $PropertyAd->facilities_paid_by_landlord = $request->facilities_paid_by_landlord;
            $PropertyAd->utilities_paid_by_tenant = $request->utilities_paid_by_tenant;
            $PropertyAd->facilities_paid_by_tenant = $request->facilities_paid_by_tenant;
            $PropertyAd->created_by = Auth::id();
            $PropertyAd->update();

            return $this->sendResponse(['id' => $PropertyAd->id], 'Property updated successfully');
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

    public function changeStatus(Request $request, $id)
    {
        try {
            $PropertyAd = PropertyAd::findOrFail($id);
            if ($request->status) {
                $PropertyAd->status = 0;
                $PropertyAd->update();

                return $this->sendResponse(['id' => $id], 'Property ad inactive successfully');
            }

            $PropertyAd->status = 1;
            $PropertyAd->update();

            return $this->sendResponse(['id' => $id], 'Property ad active successfully');
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

    public function getActivePropertyList(){
        $activePropertyAds = PropertyAd::where('status',1)
            ->with('property')
            ->get();
        return $activePropertyAds;
    }


}
