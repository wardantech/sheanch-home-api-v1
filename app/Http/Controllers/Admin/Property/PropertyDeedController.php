<?php

namespace App\Http\Controllers\Admin\Property;

use App\Http\Controllers\Controller;
use App\Models\Property\PropertyDeed;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

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

        $query = PropertyDeed::select('*')->with(['landlord','tenant','property','propertyAd'])
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

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Property\PropertyDeed  $propertyDeed
     * @return \Illuminate\Http\Response
     */
    public function show(PropertyDeed $propertyDeed)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Property\PropertyDeed  $propertyDeed
     * @return \Illuminate\Http\Response
     */
    public function edit(PropertyDeed $propertyDeed)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Property\PropertyDeed  $propertyDeed
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PropertyDeed $propertyDeed)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Property\PropertyDeed  $propertyDeed
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

            return $this->sendResponse(['id' => $id], 'Property active successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Property status error', ['error' => $exception->getMessage()]);
        }
    }
}
