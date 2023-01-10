<?php

namespace App\Http\Controllers\User\Property;


use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeedDetailsResource;
use App\Http\Resources\DeedTenantInfoResource;
use App\Models\Property\PropertyDeed;

class PropertyDeedController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        $this->middleware('auth:api', [ 'except' => ['save']]);
    }

    /**
     * List api
     * @return \Illuminate\Http\Response
     */
    public function requestDeed(Request $request)
    {
        $columns = ['id', 'name'];
        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];
        $userId = $request['params']['userId'];

        $query = PropertyDeed::with(['tenant' => function($query){
            $query->select('id', 'name');
        }, 'property' => function($query){
            $query->select('id', 'name');
        }])
        ->where('landlord_id', $userId)
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

    public function applyDeed(Request $request)
    {
        $columns = ['id', 'name'];
        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];
        $userId = $request['params']['userId'];

        $query = PropertyDeed::with(['landlord' => function($query){
            $query->select('id', 'name');
        }, 'property' => function($query){
            $query->select('id', 'name');
        }])
        ->where('tenant_id', $userId)
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
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        $data = $request->validate([
            'landlord_id' => 'required',
            'tenant_id' => 'required|unique:property_deeds',
            'property_id' => 'required',
            'property_ad_id' => 'required',
        ], [
            'tenant_id.unique' => 'You already apply on this advertisement.',
        ]);

        try {
            $data['status'] = 1;
            $deed = PropertyDeed::create($data);

            return $this->sendResponse(['id' => $deed], 'Property create successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property store error', ['error' => $exception->getMessage()]);
        }
    }

    public function show(Request $request)
    {
        try {
            $deed = PropertyDeed::findOrFail($request->deedId);

            if ($deed->landlord_id !== $request->userId) {
                throw new \Exception("User or landlord not same");
            }

            if ($deed->status === 0) {
                $deed->status = 2;
                $deed->update();
            }

            return $this->sendResponse([
                'deed' => new DeedDetailsResource($deed)
            ], 'Property create successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property store error', ['error' => $exception->getMessage()]);
        }
    }

    public function accept(Request $request)
    {
        try {
            $deed = PropertyDeed::findOrFail($request->deedId);

            if ($deed->landlord_id !== $request->userId) {
                throw new \Exception("User or landlord not same");
            }

            $deed->status = 3;
            $deed->start_date = now();
            $deed->update();

            return $this->sendResponse([
                'deed' => new DeedDetailsResource($deed)
            ], 'Deed accept successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property store error', ['error' => $exception->getMessage()]);
        }
    }

    public function tenantInfo(Request $request)
    {
        try {
            $deed = PropertyDeed::findOrFail($request->deedId);

            if ($deed->landlord_id !== $request->userId) {
                throw new \Exception("User or landlord not same");
            }

            return $this->sendResponse([
                'tenant' => new DeedTenantInfoResource($deed)
            ], 'Get tenant information successfully.');
        } catch (\Exception $exception) {
            return $this->sendError('Property store error', ['error' => $exception->getMessage()]);
        }
    }

    public function approve(Request $request)
    {
        try {
            $deed = PropertyDeed::findOrFail($request->deedId);

            if ($deed->landlord_id !== $request->userId) {
                throw new \Exception("User or landlord not same");
            }

            $deed->status = 5;
            $deed->start_date = now();
            $deed->update();

            return $this->sendResponse([
                'deed' => new DeedDetailsResource($deed)
            ], 'deed approved successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property store error', ['error' => $exception->getMessage()]);
        }
    }

    public function decline(Request $request)
    {
        try {
            $deed = PropertyDeed::findOrFail($request->deedId);

            if ($deed->landlord_id !== $request->userId) {
                throw new \Exception("User or landlord not same");
            }

            $deed->status = 0;
            $deed->update();

            return $this->sendResponse([
                'deed' => new DeedDetailsResource($deed)
            ], 'deed declined successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property store error', ['error' => $exception->getMessage()]);
        }
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
}
