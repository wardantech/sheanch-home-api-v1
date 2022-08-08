<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\FrontendSetting;
use App\Service\FrontendSettingsServices;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FrontendSettingController extends Controller
{
    use ResponseTrait;

    public function __construct()
    {
        $this->middleware(['auth:api']);
    }

    public function getData()
    {

        try {
            $general = FrontendSetting::with('media')
                ->first();

            if(!$general) {
                return [
                    'status' => false,
                    'data' => 'Data Not Found'
                ];
            }

            return [
                'status' => true,
                'data' => $general
            ];

        } catch (\Exception $exception) {
            return $this->sendError('Frontend general setting get data error', ['error' => $exception->getMessage()]);
        }
    }

    public function store(Request $request)
    {
//        return $request->footerLogo;
        //--- Validation Section Start ---//
        $rules = [
            'email' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()), 422);
        }
        //--- Validation Section Ends  ---//

        //try {
            // Store Property
            $data = FrontendSetting::first();

            if (is_null($data)) {
                $frontendSetting = new FrontendSetting();

                $frontendSetting->email = $request->email;
                $frontendSetting->phone = $request->phone;
                $frontendSetting->address = $request->address;
                $frontendSetting->save();

                $this->storeSettingsImages($frontendSetting, $request);

            } else {
                $data->email = $request->email;
                $data->phone = $request->phone;
                $data->address = $request->address;
                $data->update();

                $this->updateSettingsImages($data, $request);
            }

            return $this->sendResponse('', 'Frontend general setting updated successfully');

//        } catch (\Exception $exception) {
//            return $this->sendError('Frontend general setting store error', ['error' => $exception->getMessage()]);
//        }
    }

    private function storeSettingsImages($frontendSetting, $request){
        if ($frontendSetting && count($request->bannerImage) > 0) {
            foreach ($request->bannerImage as $image) {
                FrontendSettingsServices::imageUpload($frontendSetting, $image['data'], 'banner');
            }
        }

//        if ($frontendSetting && count($request->favicon) > 0) {
//            foreach ($request->favicon as $image) {
//                FrontendSettingsServices::imageUpload($frontendSetting, $image['data'], 'favicon');
//            }
//        }

        if ($frontendSetting && count($request->logo) > 0) {
            foreach ($request->logo as $image) {
                FrontendSettingsServices::imageUpload($frontendSetting, $image['data'], 'logo');
            }
        }

        if ($frontendSetting && count($request->footerLogo) > 0) {
            foreach ($request->footerLogo as $image) {
                FrontendSettingsServices::imageUpload($frontendSetting, $image['data'], 'footerLogo');
            }
        }
    }

    private function updateSettingsImages($data, $request){
        // Banner Image
        if($data && count($request->bannerImage) == 0){
            FrontendSettingsServices::imageDelete($data, 'banner');
        }

        if($data && count($request->bannerImage) > 0) {
            foreach ($request->bannerImage as $bannerImage) {
                if(isset($bannerImage['data'])){
                    FrontendSettingsServices::imageDelete($data,'banner');
                    FrontendSettingsServices::imageUpload($data, $bannerImage['data'], 'banner');
                }
            }
        }

        // Title Favicon
//        if($data && count($request->favicon) == 0){
//            FrontendSettingsServices::imageDelete($data, 'favicon');
//        }

//        if($data && count($request->favicon) > 0) {
//            foreach ($request->favicon as $favicon) {
//                if(isset($favicon['data'])){
//                    FrontendSettingsServices::imageDelete($data, 'favicon');
//                    FrontendSettingsServices::imageUpload($data, $favicon['data'], 'favicon');
//                }
//            }
//        }

        // Header Logo
        if($data && count($request->logo) == 0){
            FrontendSettingsServices::imageDelete($data, 'logo');
        }

        if($data && count($request->logo) > 0){
            foreach ($request->logo as $logo) {
                if(isset($logo['data'])){
                    FrontendSettingsServices::imageDelete($data, 'logo');
                    FrontendSettingsServices::imageUpload($data, $logo['data'], 'logo');
                }
            }
        }

        // Footer Logo
        if($data && count($request->footerLogo) == 0){
            FrontendSettingsServices::imageDelete($data, 'footerLogo');
        }

        if($data && count($request->footerLogo) > 0){
            foreach ($request->footerLogo as $footerLogo) {
                if(isset($footerLogo['data'])){
                    FrontendSettingsServices::imageDelete($data, 'footerLogo');
                    FrontendSettingsServices::imageUpload($data, $footerLogo['data'], 'footerLogo');
                }
            }
        }
    }
}
