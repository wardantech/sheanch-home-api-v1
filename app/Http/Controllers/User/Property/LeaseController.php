<?php

namespace App\Http\Controllers\User\Property;

use App\Http\Controllers\Controller;


use App\Models\Property\Lease;
use App\Models\Property\Property;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LeaseController extends Controller
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

        $query = Lease::select('*')->with(['landlord','property'])
            ->where('tenant_id', Auth::user()->tenant_id)
            ->orderBy($columns[$column], $dir);

        $count = Lease::count();

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
            'tenant_id' => 'required',
            'property_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//


        try {
            // Store Property
            $lease = new Lease();

            $lease->landlord_id = $request->landlord_id;
            $lease->property_id = $request->property_id;
            $lease->tenant_id = $request->tenant_id;
            $lease->status = 0;
            $lease->created_by = Auth::id();
            $lease->save();

            return $this->sendResponse(['id' => $lease->id], 'Property create successfully');
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
            $Lease = Lease::findOrFail($id);

            return $this->sendResponse($Lease, 'Property data get successfully');
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
            $Lease = Lease::findOrFail($id);

            $Lease->thana_id = $request->thana_id;
            $Lease->district_id = $request->district_id;
            $Lease->division_id = $request->division_id;
            $Lease->name = $request->name;
            $Lease->zip_code = $request->zip_code;
            $Lease->address = $request->address;
            $Lease->bed_rooms = $request->bed_rooms;
            $Lease->bath_rooms = $request->bath_rooms;
            $Lease->units = $request->units;
            $Lease->area_size = $request->area_size;
            $Lease->rent_amount = $request->rent_amount;
            $Lease->description = $request->description;
            $Lease->status = $request->status;
            $Lease->security_money = $request->security_money;
            $Lease->utilities_paid_by_landlord = $request->utilities_paid_by_landlord;
            $Lease->facilities_paid_by_landlord = $request->facilities_paid_by_landlord;
            $Lease->utilities_paid_by_tenant = $request->utilities_paid_by_tenant;
            $Lease->facilities_paid_by_tenant = $request->facilities_paid_by_tenant;
            $Lease->created_by = Auth::id();
            $Lease->update();

            return $this->sendResponse(['id' => $Lease->id], 'Property updated successfully');
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
            $Lease = Lease::findOrFail($id);
            if ($request->status) {
                $Lease->status = 0;
                $Lease->update();

                return $this->sendResponse(['id' => $id], 'Property ad inactive successfully');
            }

            $Lease->status = 1;
            $Lease->update();

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
        $activeLeases = Lease::where('status',1)
            ->with('property')
            ->get();
        return $activeLeases;
    }


}
