<?php

namespace App\Http\Controllers\User\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\District;
use App\Models\Settings\Division;
use App\Models\Settings\Thana;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class GetDivisionDistrictThanaController extends Controller
{
    use ResponseTrait;

    /**
     * Division list api
     * @return \Illuminate\Http\Response
     */
    public function getDivisions()
    {
        try {
            $divisions = Division::all();

            return $this->sendResponse($divisions, 'Division list');

        } catch (\Exception $exception) {

            return $this->sendError('Division list.', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * District api
     * received divisionId as parameter
     * @return \Illuminate\Http\Response
     */
    public function getDistricets(Request $request)
    {
        try {
            $districts = District::where('division_id', $request->divisionId)->get();

            return $this->sendResponse($districts, 'District list');

        } catch (\Exception $exception) {

            return $this->sendError('District list.', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * District api
     * received districtId as parameter
     * @return \Illuminate\Http\Response
     */
    public function getThanas(Request $request)
    {
        try {
            $thanas = Thana::where('district_id', $request->districtId)->get();

            return $this->sendResponse($thanas, 'Thana list');

        } catch (\Exception $exception) {

            return $this->sendError('Thana list.', ['error' => $exception->getMessage()]);
        }
    }
}
