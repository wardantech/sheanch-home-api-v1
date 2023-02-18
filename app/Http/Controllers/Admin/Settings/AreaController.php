<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Models\Area;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Models\Settings\Thana;
use App\Models\Settings\District;
use App\Models\Settings\Division;
use App\Http\Controllers\Controller;

class AreaController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $columns = ['id', 'name'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = Area::with(['divisions' => function($query) {
            $query->select('id', 'name');
        }, 'districts' => function($query) {
            $query->select('id', 'name');
        }, 'thanas' => function($query) {
            $query->select('id', 'name');
        }])
        ->select('*')->orderBy($columns[$column], $dir);

        $count = Area::count();

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $divisions = $this->getDivisions();

        return $this->sendResponse([
            'divisions' => $divisions
        ],'');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|string',
            'district_id' => 'required|integer',
            'division_id' => 'required|integer',
            'thana_id' => 'required|integer'
        ]);

        try {
            $areas = Area::create($data);

            return $this->sendResponse([
                'areas' => $areas
            ],'Area created successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Area store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $area = Area::findOrFail($request->areaId);
        $divisions = $this->getDivisions();
        $districts = District::select('id', 'name')->get();
        $thanas = Thana::select('id', 'name')->get();

        return $this->sendResponse([
            'area' => $area,
            'divisions' => $divisions,
            'districts' => $districts,
            'thanas' => $thanas
        ], 'Area fetch successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Area $area)
    {
        $data = $this->validate($request, [
            'name' => 'required|string',
            'district_id' => 'required|integer',
            'division_id' => 'required|integer',
            'thana_id' => 'required|integer'
        ]);

        try {
            $area->update($data);

            return $this->sendResponse([
                'area' => $area
            ], 'Area updated successfully');
        }catch(\Exception $exception) {
            return $this->sendError('Area edit error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Area $area)
    {
        try {
            $area->delete();
            return $this->sendResponse([
                'area' => $area
            ], 'Area deleted successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Area delete error', ['error' => $exception->getMessage()]);
        }
    }

    private function getDivisions()
    {
        return Division::select('id', 'name')->get();
    }

    public function getDistricts(Request $request)
    {
        $districts = District::where('division_id', $request->divisionId)
                        ->select('id', 'name')
                        ->get();

        return $this->sendResponse([
            'districts' => $districts
        ],'');
    }

    public function getThanas(Request $request)
    {
        $thanas = Thana::where('district_id', $request->thanaId)
                    ->select('id', 'name')
                    ->get();

        return $this->sendResponse([
            'thanas' => $thanas
        ],'');
    }
}
