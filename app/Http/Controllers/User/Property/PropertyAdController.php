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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PropertyAdController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        $this->middleware('auth:api',
            [
                'except' => ['getActivePropertyList', 'getDetails', 'search']
            ]
        );
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

        $query = PropertyAd::select('*')->with(['landlord', 'property'])
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
            'rent_amount' => 'required',
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
     * Property single data get for or details
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function getDetails(Request $request)
    {
        try {
            $PropertyAd = PropertyAd::where('id', $request->propertyAdId)
                ->with(['property' => function ($query) {
                    $query->with('media');
                }])
                ->first();

            return $this->sendResponse($PropertyAd, 'Property data get successfully');

        } catch (\Exception $exception) {
            return $this->sendError('Property data error', ['error' => $exception->getMessage()]);
        }
    }

    public function getEditData(Request $request)
    {

        try {
            $PropertyAd = PropertyAd::findOrFail($request->id);
            $properties = Property::where('landlord_id', $request->landlordId)
                ->where('status', true)->get();

            $data = [
                'propertyAd' => $PropertyAd,
                'properties' => $properties,
            ];

            return $this->sendResponse($data, 'Property Ad data get successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property Ad data error', ['error' => $exception->getMessage()]);
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
        //return $request->input();
        //--- Validation Section Start ---//
        $rules = [
            'landlord_id' => 'required',
            'rent_amount' => 'required',
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
            $PropertyAd = PropertyAd::findOrFail($id);

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
            $PropertyAd->updated_by = Auth::id();
            $PropertyAd->update();

            return $this->sendResponse(['id' => $PropertyAd->id], 'Property Ad update successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property Ad update error', ['error' => $exception->getMessage()]);
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
            $properties = Property::where('landlord_id', $request->landlordId)
                ->where('status', true)->get();

            return $this->sendResponse($properties, 'Landlord list');

        } catch (\Exception $exception) {

            return $this->sendError('Landlord list.', ['error' => $exception->getMessage()]);
        }
    }

    public function getActivePropertyList()
    {

        try {
            $activePropertyAds = PropertyAd::where('status', 1)
                ->with(['property' => function ($query) {
                    $query->with('media');
                }])->get();

            return $this->sendResponse($activePropertyAds, 'Property data get successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property data error', ['error' => $exception->getMessage()]);
        }
    }

    public function getActivePropertyListAsType(Request $request)
    {

        try {
            $activePropertyAds = PropertyAd::where('status', 1)
                ->where('sale_type',$request->type)
                ->with(['property' => function ($query) {
                    $query->with('media');
                }])->get();

            return $this->sendResponse($activePropertyAds, 'Property data get successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property data error', ['error' => $exception->getMessage()]);
        }
    }

    public function search(Request $request)
    {
        try {
            $search = PropertyAd::where('status', 1);

            if (isset($request->sale_type)) {
                $search->where('sale_type', $request->sale_type);
            }
            if (isset($request->min_price)) {
                $search->where('rent_amount', '>=', $request->min_price);
            }
            if (isset($request->max_price)) {
                $search->where('rent_amount', '<=', $request->max_price);
            }
            if (isset($request->property_category)) {
                $search->where('property_category', $request->property_category);
            }
            if (isset($request->property_type_id)) {
                $search->where('property_type_id', $request->property_type_id);
            }
            if (isset($request->division_id)) {
                $search->where('division_id', $request->division_id);
            }
            if (isset($request->district_id)) {
                $search->where('district_id', $request->district_id);
            }
            if (isset($request->thana_id)) {
                $search->where('thana_id', $request->thana_id);
            }

            $result = $search->with(['property' => function ($query) {
                $query->with('media');
            }])->get();

            return $this->sendResponse($result, 'Search data get successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Search data error', ['error' => $exception->getMessage()]);
        }

    }
}
