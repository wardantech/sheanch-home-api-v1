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

            if($general){
                $logo = $general->getMedia($request->data);
                if(count($logo) > 0){
                    $url = $logo[0]->getFullUrl();
                }else {
                    $url = 'https://i.ibb.co/1MFFM3W/Image-not-available.png';
                }
            }else {
                return [
                    'status' => false,
                    'image' => 'Image Not Found',
                    'data' => 'Data Not Found'
                ];
            }

            return [
                'status' => true,
                'image' => $url,
                'data' => $general
            ];

        }catch (\Exception $exception) {
            return $this->sendError('Frontend general setting get data error', ['error' => $exception->getMessage()]);
        }
    }
}
