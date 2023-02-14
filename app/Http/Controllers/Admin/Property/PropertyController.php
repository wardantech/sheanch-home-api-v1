<?php

namespace App\Http\Controllers\Admin\Property;

use App\Models\User;
use App\Models\Landlord;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Models\Settings\Thana;
use App\Models\Settings\Utility;
use App\Models\Property\Property;
use App\Models\Settings\District;
use App\Models\Settings\Division;
use App\Models\Settings\Facility;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Settings\PropertyType;
use App\Http\Resources\FacilityResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Resources\PropertyShowResource;
use App\Service\PropertyService;

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
            [$users, $propertyTypes, $division, $utilities, $facilities] = PropertyService::getPropertyData();

            return $this->sendResponse([
                    'users' => $users,
                    'propertyTypes' => $propertyTypes,
                    'divisions' => $division,
                    'utilities' => $utilities,
                    'facilities' => FacilityResource::collection($facilities)
            ], 'Property data get successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Property data error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Store api
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

    /**
     * Property single data get for update or show
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $property = Property::findOrFail($id);
            $images = PropertyService::getImages($property->getMedia());
            $facilitieIds = json_decode($property->facilitie_ids);
            $facilities = Facility::whereIn('id', $facilitieIds)->get('name');

            return $this->sendResponse([
                'images' => $images,
                'facilities' => $facilities,
                'property' => new PropertyShowResource($property)
            ], 'Property data get successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property data error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Property single data get for edit
     *
     * @param  mixed $request
     * @return void
     */
    public function edit(Request $request)
    {
        try {
            $property = Property::findOrFail($request->id);
            $propertyImages = PropertyService::getImages($property->getMedia());

            [$users, $propertyTypes, $division, $utilities, $facilities] = PropertyService::getPropertyData();

            $district = District::where('division_id', $property->division_id)->get();
            $thana = Thana::where('district_id', $property->district_id)->get();

            return $this->sendResponse([
                'users' => $users,
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

    /**
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
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
     * Status Active or Inactive
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     */

    public function changeStatus(Request $request, $id)
    {
        try {
            $property = Property::findOrFail($id);
            if ($request->status) {
                $property->status = 0;
                $property->update();

                return $this->sendResponse(['id' => $id], 'Property inactive successfully');
            }

            $property->status = 1;
            $property->update();

            return $this->sendResponse(['id' => $id], 'Property active successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property status error', ['error' => $exception->getMessage()]);
        }
    }


    /**
     * Property Data Delete
     * @param $id
     * @return mixed
     */

    public function destroy($id)
    {
        try {
            $property = Property::findOrFail($id);
            $property->delete();

            return $this->sendResponse(['id' => $id], 'Property deleted successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property delete error', ['error' => $exception->getMessage()]);
        }
    }
}
