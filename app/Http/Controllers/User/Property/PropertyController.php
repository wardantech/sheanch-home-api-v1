<?php

namespace App\Http\Controllers\User\Property;

use App\Http\Controllers\Controller;
use App\Models\Landlord;
use App\Models\Property\Property;
use App\Models\Settings\District;
use App\Models\Settings\Division;
use App\Models\Settings\Facility;
use App\Models\Settings\FacilityCategory;
use App\Models\Settings\PropertyType;
use App\Models\Settings\Thana;
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
        $this->middleware('auth:api', ['except' => ['show','getPropertyTypes']]);
    }

    public function getList(Request $request)
    {
//        return Auth::user()->landlord_id;
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
            'security_money' => 'required',
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
            $property->property_category = $request->property_category;
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
            $property->utilities = json_encode($request->utilities);
            $property->facilities = json_encode($request->facilities);
            $property->created_by = Auth::id();
            $property->save();

            if ($property && count($request->images) > 0) {
                foreach ($request->images as $image) {

                    $property->addMediaFromBase64($image['data'])
                        ->usingFileName(uniqid('property', false) . '.png')
                        ->toMediaCollection();

                }
            }

            return $this->sendResponse(['id' => $property->id], 'Property create successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property store error', ['error' => $exception->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        try {
            $property = Property::findOrFail($request->id);
            $propertyImages = $property->getMedia();
            $propertyImagesData = [];

            foreach ($propertyImages as $propertyImage) {
                $propertyImagesUrl = [];

                $path = $propertyImage->getPath();
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64 = 'data:application/' . $type . ';base64,' . base64_encode($data);

                $propertyImagesUrl ['url'] = $propertyImage->original_url;
                $propertyImagesUrl ['data'] = $base64;
                $propertyImagesUrl ['size'] = $propertyImage->size;
                $propertyImagesUrl ['name'] = $propertyImage->file_name;

                $propertyImagesData [] = $propertyImagesUrl;
            }

            $landlords = Landlord::all();
            $propertyTypes = PropertyType::all();
            $division = Division::all();
            $district = District::where('division_id', $property->division_id)->get();
            $thana = Thana::where('district_id', $property->district_id)->get();
            $utilities = Utility::all();
            $facilities = Facility::all();

            return $this->sendResponse([
                'property' => $property,
                'propertyImages' => $propertyImagesData,
                'landlords' => $landlords,
                'propertyTypes' => $propertyTypes,
                'divisions' => $division,
                'districts' => $district,
                'thanas' => $thana,
                'utilities' => $utilities,
                'facilities' => $facilities
            ], 'Property edit data get successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Property data error', ['error' => $exception->getMessage()]);
        }
    }

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
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Update Property
            $property = Property::findOrFail($id);

            $property->thana_id = $request->thana_id;
            $property->district_id = $request->district_id;
            $property->division_id = $request->division_id;
            $property->property_type_id = $request->property_type_id;
            $property->landlord_id = $request->landlord_id;
            $property->name = $request->name;
            $property->zip_code = $request->zip_code;
            $property->property_category = $request->property_category;
            $property->sale_type = $request->sale_type;
            $property->house_no = $request->house_no;
            $property->address = $request->address;
            $property->bed_rooms = $request->bed_rooms;
            $property->bath_rooms = $request->bath_rooms;
            $property->area_size = $request->area_size;
            $property->rent_amount = $request->rent_amount;
            $property->description = $request->description;
            $property->status = $request->status;
            $property->security_money = $request->security_money;
            $property->utilities = json_encode($request->utilities);
            $property->facilities = json_encode($request->facilities);
            $property->updated_by = Auth::id();
            $property->update();

            $mediaItems = $property->getMedia();
            if(count($mediaItems) > 0){
                foreach ($mediaItems as $mediaItem) {
                    $mediaItem->delete();
                }
            }

            if ($property && $request->images && count($request->images) > 0) {
                foreach ($request->images as $image) {

                    $property->addMediaFromBase64($image['data'])
                        ->usingFileName(uniqid('property', false) . '.png')
                        ->toMediaCollection();
                }
            }

            if ($property && $request->oldImages && count($request->oldImages) > 0) {
                foreach ($request->oldImages as $image) {
                    $property->addMediaFromBase64($image['data'])
                        ->usingFileName(uniqid('property', false) . '.png')
                        ->toMediaCollection();
                }
            }

            return $this->sendResponse(['id' => $property->id], 'Property updated successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property updated error', ['error' => $exception->getMessage()]);
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

    public function create()
    {
        try {
            $propertyTypes = PropertyType::all();
            $division = Division::all();
            $utility = Utility::all();
            $facilities = Facility::all();

            return $this->sendResponse(
                [
                    'propertyTypes' => $propertyTypes,
                    'divisions' => $division,
                    'utilities' => $utility,
                    'facilities' => $facilities
                ],
                'Property data get successfully');

        } catch (\Exception $exception) {
            return $this->sendError('Property data error', ['error' => $exception->getMessage()]);
        }
    }
}
