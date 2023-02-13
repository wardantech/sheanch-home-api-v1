<?php

namespace App\Http\Controllers\User\Property;

use App\Models\Landlord;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Models\Settings\Thana;
use App\Models\Settings\Utility;
use App\Service\PropertyService;
use App\Models\Property\Property;
use App\Models\Settings\District;
use App\Models\Settings\Division;
use App\Models\Settings\Facility;
use App\Http\Controllers\Controller;
use App\Models\Accounts\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Models\Property\PropertyDeed;
use App\Models\Settings\PropertyType;
use App\Http\Resources\FacilityResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StorePropertyRequest;

class PropertyController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['show', 'getPropertyTypes']]);
    }

    public function getList(Request $request)
    {
        $columns = ['id', 'name'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = Property::where('user_id', Auth::id())->select('*')->orderBy($columns[$column], $dir);

        $count = Property::count();

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

    public function create()
    {
        try {
            [$users, $propertyTypes, $division, $utility, $facilities] = PropertyService::getPropertyData();

            return $this->sendResponse([
                'propertyTypes' => $propertyTypes,
                'divisions' => $division,
                'utilities' => $utility,
                'facilities' => FacilityResource::collection($facilities)
            ], 'Property data get successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property data error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Store properties
     * @return \Illuminate\Http\Response
     */
    public function store(StorePropertyRequest $request)
    {
        try {
            $data = $request->validated();

            $totalRent = PropertyService::totalRentAmount($data['utilities'], $data['rent_amount']);

            $data['total_amount'] = $totalRent;
            $data['utilities'] = json_encode($data['utilities']);
            $data['facilitie_ids'] = json_encode($data['facilitie_ids']);
            $data['created_by'] = $data['user_id'];

            $property = Property::create($data);

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
            $propertyImages = PropertyService::getImages($property->getMedia());

            [$users, $propertyTypes, $division, $utilities, $facilities] = PropertyService::getPropertyData();

            $district = District::where('division_id', $property->division_id)->get();
            $thana = Thana::where('district_id', $property->district_id)->get();

            return $this->sendResponse([
                'property' => $property,
                'propertyImages' => $propertyImages,
                'propertyTypes' => $propertyTypes,
                'divisions' => $division,
                'districts' => $district,
                'thanas' => $thana,
                'utilities' => $utilities,
                'facilities' => FacilityResource::collection($facilities)
            ], 'Property edit data get successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Property data error', ['error' => $exception->getMessage()]);
        }
    }

    public function update(StorePropertyRequest $request, $id)
    {
        try {
            $data = $request->validated();

            $totalRent = PropertyService::totalRentAmount($data['utilities'], $data['rent_amount']);

            $data['total_amount'] = $totalRent;
            $data['utilities'] = json_encode($data['utilities']);
            $data['facilitie_ids'] = json_encode($data['facilitie_ids']);
            $data['updated_by'] = $data['user_id'];

            $property = Property::findOrFail($id);
            $property->update($data);

            $mediaItems = $property->getMedia();
            if (count($mediaItems) > 0) {
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
        try {
            $property = Property::where('id', $id)->with('propertyType', 'landlord', 'media')->first();
            return $this->sendResponse($property, 'Property data get successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property data error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Show property details
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function details($id)
    {
        try {
            $property = Property::with('thana', 'district', 'division', 'propertyType', 'landlord', 'media')
                ->findOrFail($id);

            return $this->sendResponse($property, 'Property data get successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property data error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Show property owner details
     * @param $id
     * @return mixed
     */

    public function landlordDetails($id)
    {
        try {
            $property = Property::with(['landlord' => function ($query) {
                $query->with(['division' => function ($query) {
                    $query->select('id', 'name');
                }, 'district' => function ($query) {
                    $query->select('id', 'name');
                }, 'thana' => function ($query) {
                    $query->select('id', 'name');
                }, 'reviews']);
            }])->findOrFail($id);

            $landlord = $property->landlord;
            $rating = $landlord->reviews()->avg('rating');

            return $this->sendResponse([
                'landlord' => $landlord,
                'rating' => $rating
            ], 'Landlord data get successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Landlord data error', ['error' => $exception->getMessage()]);
        }
    }

    public function paymentReports(Request $request)
    {
        $columns = ['id', 'name'];
        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];
        $propertyId = $request['params']['propertyId'];
        $userId = $request['params']['userId'];

        $query = Transaction::with('due', 'property')
            ->where('user_id', $userId)
            ->where('property_id', $propertyId)
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
}
