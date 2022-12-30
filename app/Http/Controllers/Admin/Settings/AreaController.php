<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Models\Area;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Models\Settings\Thana;
use App\Models\Settings\District;
use App\Models\Settings\Division;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AreaController extends Controller
{
    use ResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $area = Area::with('division','district','thana')->get();
            // $area = Area::get();
            // // return  $area;
            // $district = [];
            // $division = [];
            // $thana = [];
            // foreach( $area as $a){
            //     $districtId = $a->district_id;
            //     $divitsonId = $a->division_id;
            //     $thanaId = $a->thana_id;
            //     $district[] = District::where('id', $districtId)->first();
            //     $division[] = Division::where('id', $divitsonId)->first();
            //     $thana[] = Thana::where('id', $thanaId)->first();
            // }
            // $area1 = [
            //     'area' => $area,
            //     'division' => $division,
            //     'district' => $district,
            //     'thana' => $thana
            // ];
            return $this->sendResponse([
                'area' => $area,
            ], 'Get all data successfully');
        }
        catch (\Exception $exception){
            return $this->sendError('Area data error', ['error' => $exception->getMessage()]);
        }
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       //--- Validation Section Start ---//
       $rules = [
        'name' => 'required|string|max:255',
    ];
    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
    }
    //--- Validation Section Ends  ---//

    try {
        // Store Utility
        $area = new Area();
        $area->division_id = $request->division_id;
        $area->district_id = $request->district_id;
        $area->thana_id = $request->thana_id;
        $area->name = $request->name;
        $area->bn_name = $request->bn_name;
        $area->save();
        return $this->sendResponse(['id'=>$area->id],'Area create successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Area store error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $area = Area::findOrFail($id);
            return $this->sendResponse([
                'area' =>$area
            ], 'Area fetch successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Area fetch error', ['error' => $exception->getMessage()]);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //--- Validation Section Start ---//
        $rules = [
            'name' => 'required|string|max:255',
        ];
        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//
    
        try {
            // Store Utility
            $area = Area::findOrFail($id);
            $area->division_id = $request->division_id;
            $area->district_id = $request->district_id;
            $area->thana_id = $request->thana_id;
            $area->name = $request->name;
            $area->bn_name = $request->bn_name;
            $area->update();
            return $this->sendResponse(['id'=>$area->id],'Area edit successfully');
            } catch (\Exception $exception) {
                return $this->sendError('Area edit error', ['error' => $exception->getMessage()]);
            }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $area = Area::findOrFail($id);
            $area->delete();
            return $this->sendResponse(['id' => $id], 'Area deleted successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Area delete error', ['error' => $exception->getMessage()]);
        }
    }
}
