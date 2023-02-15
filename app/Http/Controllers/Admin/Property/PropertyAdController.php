<?php

namespace App\Http\Controllers\Admin\Property;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyAdRequest;
use App\Models\Landlord;

use App\Models\Property\Property;
use App\Models\Property\PropertyAd;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyAdController extends Controller
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

        $query = PropertyAd::select('*')->with(['landlord','property'])
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
     * Get all
     *
     * @param  mixed $request
     * @return void
     */
    public function create(Request $request)
    {
        $users = User::where('is_admin', 0)
            ->where('status', 1)
            ->select('id', 'name')
            ->get();

        return $this->sendResponse([
            'users' => $users
        ], 'get all users successfully');
    }

    /**
     * Store api
     * @return \Illuminate\Http\Response
     */
    public function store(StorePropertyAdRequest $request)
    {
        $data = $request->validated();

        try {
            $data['created_by'] = Auth::id();
            $data['property_category'] = $data['property_category'] == 'Commercial' ? 1: 2;

            $propertyAd = PropertyAd::create($data);

            return $this->sendResponse([
                'id' => $propertyAd->id
            ], 'Property create successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property store error', [
                'error' => $exception->getMessage()
            ]);
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

    public function edit(Request $request)
    {
        try {
            $propertyAd = PropertyAd::findOrFail($request->id);
            $users = User::all(['id','name']);
            $properties = Property::where('user_id', $propertyAd->user_id)
                ->where('status', true)->get();

            return $this->sendResponse([
                'propertyAd' =>  $propertyAd,
                'properties' =>  $properties,
                'users' =>  $users,
            ], 'Property Ad data get successfully');
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
    public function update(StorePropertyAdRequest $request, $id)
    {
        $data = $request->validated();

        try {
            $data['updated_by'] = Auth::id();
            $data['property_category'] = $data['property_category'] == 'Commercial' ? 1: 2;

            $propertyAd = PropertyAd::findOrFail($id);
            $propertyAd->update($data);

            return $this->sendResponse([
                'id' => $propertyAd->id
            ], 'Property updated successfully');
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

                return $this->sendResponse(['id' => $id], 'Property inactive successfully');
            }

            $PropertyAd->status = 1;
            $PropertyAd->update();

            return $this->sendResponse(['id' => $id], 'Property active successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property status error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * PropertyAd Data Delete
     * @param $id
     * @return mixed
     */

    public function destroy($id)
    {
        try {
            $propertyAd = PropertyAd::findOrFail($id);
            $propertyAd->delete();

            return $this->sendResponse(['id'=>$id],'Property Ad deleted successfully');
        }catch (\Exception $exception){
            return $this->sendError('Property Ad delete error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Get all property dependency by user
     *
     * @param  mixed $request
     * @return void
     */
    public function getProperty(Request $request)
    {
        try {
            $properties = Property::where('user_id', $request->userId)
                ->where('status', 1)->get();

            return $this->sendResponse([
                'properties' =>  $properties
            ],
                'Property Ad data get successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property Ad data error', ['error' => $exception->getMessage()]);
        }
    }
}
