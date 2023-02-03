<?php

namespace App\Http\Controllers\User\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\Division;
use App\Models\Settings\FrontendSetting;
use App\Models\Settings\PropertyType;
use App\Models\Wishlist;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    use ResponseTrait;

    public function getFrontendData(Request $request)
    {
        try {
            $wishlist = '';
            $frontendData = FrontendSetting::with('media')->first();

            if($request->userId){
                $wishlist = Wishlist::where('user_id', $request->userId)->count();
            }

            return $this->sendResponse([
                'frontendData' => $frontendData,
                'wishlistCount' => $wishlist,
            ], 'Settings data get successfully');
        }catch (\Exception $exception) {
            return $this->sendError('Frontend general setting get data error', ['error' => $exception->getMessage()]);
        }
    }

    public function getFrontendBannerData(){
        try {
            $propertyTypes = PropertyType::all();
            $divisions = Division::all();

            return $this->sendResponse(
                [
                    'propertyType' => $propertyTypes,
                    'divisions' => $divisions,
                ],
                'Banner data get successfully');

        }catch (\Exception $exception) {
            return $this->sendError('Banner data error', ['error' => $exception->getMessage()]);
        }
    }
}
