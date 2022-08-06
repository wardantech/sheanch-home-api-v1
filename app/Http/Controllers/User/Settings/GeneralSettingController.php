<?php

namespace App\Http\Controllers\User\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\FrontendSetting;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    public function getGeneralSettingImages(Request $request)
    {
        try {
            $general = FrontendSetting::first();
            $logo = $general->getMedia($request->data);
            $url = $logo[0]->getFullUrl();

            if(!$general) {
                return [
                    'status' => false,
                    'data' => 'Data Not Found'
                ];
            }

            return [
                'status' => true,
                'data' => $url
            ];

        }catch (\Exception $exception) {
            return $this->sendError('Frontend general setting get data error', ['error' => $exception->getMessage()]);
        }
    }
}
