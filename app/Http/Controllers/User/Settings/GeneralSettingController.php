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
            $general = FrontendSetting::with('media')->first();
            return $this->sendResponse($general,'Settings data get successfully');

        }catch (\Exception $exception) {
            return $this->sendError('Frontend general setting get data error', ['error' => $exception->getMessage()]);
        }
    }
}
