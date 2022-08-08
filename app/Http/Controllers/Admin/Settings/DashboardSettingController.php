<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\DashboardSetting;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class DashboardSettingController extends Controller
{
    use ResponseTrait;

    /**
     * Store api
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //--- Validation Section Start ---//
        $rules = [
            'company_name' => 'string|required',
            'contact_email' => 'email|required',
            'contact_number_one' => 'string|required',
            'contact_number_two' => 'string|nullable',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Store DashboardSetting
            $dashboardSetting = new DashboardSetting();
            $dashboardSetting->company_name = $request->company_name;
            $dashboardSetting->contact_email = $request->contact_email;
            $dashboardSetting->contact_number_one = $request->contact_number_one;
            $dashboardSetting->contact_number_two = $request->contact_number_two;
            $dashboardSetting->created_by = Auth::user()->id;
            $dashboardSetting->save();

            return $this->sendResponse(['id'=>$dashboardSetting->id],'Dashboard-Setting create successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Dashboard-Setting store error', ['error' => $exception->getMessage()]);
        }
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
            'company_name' => 'string|required',
            'contact_email' => 'email|required',
            'contact_number_one' => 'string|required',
            'contact_number_two' => 'string|nullable',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        try {
            // Store DashboardSetting
            $dashboardSetting = DashboardSetting::findOrFail($id);
            $dashboardSetting->company_name = $request->company_name;
            $dashboardSetting->contact_email = $request->contact_email;
            $dashboardSetting->contact_number_one = $request->contact_number_one;
            $dashboardSetting->contact_number_two = $request->contact_number_two;
            $dashboardSetting->updated_by = Auth::user()->id;
            $dashboardSetting->save();

            return $this->sendResponse(['id'=>$dashboardSetting->id],'Dashboard-Setting update successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Dashboard-Setting update error', ['error' => $exception->getMessage()]);
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
        //
    }

}
