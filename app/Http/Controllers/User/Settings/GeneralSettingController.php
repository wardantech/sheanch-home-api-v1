<?php

namespace App\Http\Controllers\User\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\FrontendSetting;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    use ResponseTrait;

    public function getGeneralSettingImages(Request $request)
    {
        try {
            $general = FrontendSetting::first();

            if($general){
                $logo = $general->getMedia($request->data);
                if(count($logo) > 0){
                    $url = $logo[0]->getFullUrl();
                }else {
                    $url = 'no-banner.jpg';
                }
            }else {
                return [
                    'status' => false,
                    'image' => 'no-banner.jpg',
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
