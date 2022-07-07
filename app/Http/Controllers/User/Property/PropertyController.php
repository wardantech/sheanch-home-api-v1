<?php

namespace App\Http\Controllers\User\Property;

use App\Http\Controllers\Controller;
use App\Models\Property\Property;
use App\Models\Settings\FacilityCategory;
use App\Models\Settings\PropertyType;
use App\Models\Settings\Utility;
use App\Models\Settings\UtilityCategory;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['show']]);
    }

    public function getList(Request $request)
    {
        $columns = ['id', 'name'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = Property::where('landlord_id', Auth::user()->landlord_id)->select('*')->orderBy($columns[$column], $dir);

        $count = Property::count();

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
            'property_type_id' => 'required',
            'name' => 'required|string|max:255',
            'zip_code' => 'required|string|max:255',
            'address' => 'required|string',
            'bed_rooms' => 'integer|nullable',
            'bath_rooms' => 'integer|nullable',
            'units' => 'integer|nullable',
            'area_size' => 'integer|nullable',
            'rent_amount' => 'required',
            'landlord_id' => 'nullable|integer',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Store Property
            $property = new Property();

            $property->thana_id = $request->thana_id;
            $property->district_id = $request->district_id;
            $property->division_id = $request->division_id;
            $property->property_type_id = $request->property_type_id;
            $property->landlord_id = $request->landlord_id;
            $property->name = $request->name;
            $property->zip_code = $request->zip_code;
            $property->lease_type = $request->lease_type;
            $property->sale_type = $request->sale_type;
            $property->house_no = $request->house_no;
            $property->address = $request->address;
            $property->bed_rooms = $request->bed_rooms;
            $property->bath_rooms = $request->bath_rooms;
            $property->units = $request->units;
            $property->area_size = $request->area_size;
            $property->rent_amount = $request->rent_amount;
            $property->description = $request->description;
            $property->status = 0;
            $property->security_money = $request->security_money;
            $property->utilities_paid_by_landlord = json_encode($request->utilities_paid_by_landlord);
            $property->utilities_paid_by_tenant = json_encode($request->utilities_paid_by_tenant);
            $property->facilities = json_encode($request->facilities);
            $property->created_by = Auth::id();
            $property->save();

            return $this->sendResponse(['id'=>$property->id],'Property create successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Image upload for landlord
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */

    public function imageUpload(Request $request, $id)
    {
        try{
            $imageName = uniqid('property-',false).'.'.$request->file->getClientOriginalExtension();
            $request->file->move(public_path('images'), $imageName);

            $property = Property::findOrFail($id);

            if($property->image != ''){
                $img =  $property->image.','.$imageName;
            }else{
                $img = $imageName;
            }

            $property->image = $img;
            $property->update();

            return response()->json(['success'=>'You have successfully upload file.']);
        }
        catch (\Exception $exception){
            return $this->sendError('Property Image error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Get all Property Types
     * @return \Illuminate\Http\Response
     */

    public function getPropertyTypes()
    {
        try {
            $propertyTypes = PropertyType::where('status', true)->get();

            return $this->sendResponse($propertyTypes, 'Property type categories list');

        } catch (\Exception $exception) {

            return $this->sendError('Property type list.', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Get Utilities With Category
     * @return \Illuminate\Http\Response
     */

    public function getUtilities()
    {
        try {
            $utility = UtilityCategory::where('status', 1)
                ->with(['utilities' => function ($query) {
                    $query->where('status', 1);
                    $query->select(['id', 'name', 'utility_category_id']);
                }])->get(['id','name']);
            return $this->sendResponse($utility, 'Utility list');

        } catch (\Exception $exception) {

            return $this->sendError('Utility list.', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Get all Facility
     * @return \Illuminate\Http\Response
     */

    public function getFacilities()
    {
        try {
            $facility = FacilityCategory::where('status', 1)
                ->with(['facilities' => function ($query) {
                    $query->where('status', 1);
                    $query->select(['id', 'name', 'facility_category_id']);
                }])->get(['id','name']);
            return $this->sendResponse($facility, 'Facility list');

        } catch (\Exception $exception) {

            return $this->sendError('Facility list.', ['error' => $exception->getMessage()]);
        }
    }

    public function show($id)
    {
        try{
            $property = Property::findOrFail($id);
            $property->load('propertyType','landlord');
            $utility_paid_by_landlord = Utility::whereIn('id',json_decode($property->utilities_paid_by_landlord))->get();
            $utilities_paid_by_tenant = Utility::whereIn('id',json_decode($property->utilities_paid_by_tenant))->get();
            $property['utilities_paid_by_landlord'] = $utility_paid_by_landlord;
            $property['utilities_paid_by_tenant'] = $utilities_paid_by_tenant;

            return $this->sendResponse($property,'Property data get successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Property data error', ['error' => $exception->getMessage()]);
        }
    }
}
