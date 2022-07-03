<?php

namespace App\Http\Controllers\Admin\Property;

use App\Http\Controllers\Controller;
use App\Models\Property\Property;
use App\Models\Settings\PropertyType;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
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

        $query = Property::select('*')->orderBy($columns[$column], $dir);

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
            $property->status = $request->status;
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
     * Property single data get for update or show
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        try{
            $property = Property::findOrFail($id);

            return $this->sendResponse($property,'Property data get successfully');
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
            $property = Property::findOrFail($id);

            $property->thana_id = $request->thana_id;
            $property->district_id = $request->district_id;
            $property->division_id = $request->division_id;
            $property->name = $request->name;
            $property->zip_code = $request->zip_code;
            $property->address = $request->address;
            $property->bed_rooms = $request->bed_rooms;
            $property->bath_rooms = $request->bath_rooms;
            $property->units = $request->units;
            $property->area_size = $request->area_size;
            $property->rent_amount = $request->rent_amount;
            $property->description = $request->description;
            $property->status = $request->status;
            $property->security_money = $request->security_money;
            $property->utilities_paid_by_landlord = $request->utilities_paid_by_landlord;
            $property->facilities_paid_by_landlord = $request->facilities_paid_by_landlord;
            $property->utilities_paid_by_tenant = $request->utilities_paid_by_tenant;
            $property->facilities_paid_by_tenant = $request->facilities_paid_by_tenant;
            $property->created_by = Auth::id();
            $property->update();

            return $this->sendResponse(['id'=>$property->id],'Property updated successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property updated error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Get All Property Types
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
     * Status Active or Inactive
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function changeStatus(Request $request, $id)
    {
        try{
            $property = Property::findOrFail($id);
            if($request->status) {
                $property->status = 0;
                $property->update();

                return $this->sendResponse(['id'=>$id],'Property inactive successfully');
            }

            $property->status = 1;
            $property->update();

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
}
