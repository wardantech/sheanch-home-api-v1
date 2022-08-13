<?php

namespace App\Http\Controllers\User\Property;

use App\Http\Controllers\Controller;
use App\Models\Property\PropertyAd;
use App\Models\Property\PropertyDeed;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PropertyDeedController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * List api
     * @return \Illuminate\Http\Response
     */
    public function getListTenant(Request $request)
    {
        $columns = ['id', 'name'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = PropertyDeed::select('*')->with(['landlord','property','propertyAd'])
            ->where('tenant_id', Auth::user()->tenant_id)
            ->orderBy($columns[$column], $dir);

        $count = PropertyDeed::count();

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
     * List api
     * @return \Illuminate\Http\Response
     */
    public function getListLandlord(Request $request)
    {
        $columns = ['id', 'name'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = PropertyDeed::select('*')->with(['tenant','property','propertyAd'])
            ->where('landlord_id', Auth::user()->landlord_id)
            ->whereIn('status', [1,2,3])
            ->orderBy($columns[$column], $dir);

        $count = PropertyDeed::count();

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
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
            $deed = new PropertyDeed();

            $deed->landlord_id = $request->landlord_id;
            $deed->property_id = $request->property_id;
            $deed->property_ad_id = $request->property_ad_id;
            $deed->tenant_id = $request->tenant_id;
            $deed->status = 0;
            $deed->created_by = Auth::id();
            $deed->save();

            return $this->sendResponse(['id' => $deed->id], 'Property create successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Property\PropertyDeed $propertyDeed
     * @return \Illuminate\Http\Response
     */
    public function show(PropertyDeed $propertyDeed)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Property\PropertyDeed $propertyDeed
     * @return \Illuminate\Http\Response
     */
    public function edit(PropertyDeed $propertyDeed)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Property\PropertyDeed $propertyDeed
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PropertyDeed $propertyDeed)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Property\PropertyDeed $propertyDeed
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $propertyAd = PropertyDeed::findOrFail($id);
            $propertyAd->delete();

            return $this->sendResponse(['id'=>$id],'Property Deed deleted successfully');
        }catch (\Exception $exception){
            return $this->sendError('Property Deed delete error', ['error' => $exception->getMessage()]);
        }
    }
    public function changeStatus(Request $request, $id)
    {
        try {
            $lease = PropertyDeed::findOrFail($id);
            $lease->status = $request->status;
            $lease->update();

            if($request->status == 2){
                $property_ad = PropertyAd::findOrFail($lease->property_ad_id);
                $property_ad->status = 2;
                $property_ad->update();
            }

            return $this->sendResponse(['id' => $id], 'Property Deed status change successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property Deed status change error', ['error' => $exception->getMessage()]);
        }
    }

}
