<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\FrontendSetting;
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

    public function getData(Request $request)
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

        try {
            // Store Property
            $data = FrontendSetting::first();
            $frontendSetting = new FrontendSetting();

            if (is_null($data)) {
                $frontendSetting->email = $request->email;
                $frontendSetting->phone = $request->phone;
                $frontendSetting->address = $request->address;
                $frontendSetting->save();
            } else {
                $data->email = $request->email;
                $data->phone = $request->phone;
                $data->address = $request->address;
                $data->update();
            }

            if ($frontendSetting && count($request->bannerImage) > 0) {
                foreach ($request->bannerImage as $image) {
                    $frontendSetting->addMediaFromBase64($image['data'])
                        ->usingFileName(uniqid('banner_image', false) . '.png')
                        ->toMediaCollection('banner');
                }
            }

            if ($frontendSetting && count($request->favicon) > 0) {
                foreach ($request->favicon as $image) {
                    $frontendSetting->addMediaFromBase64($image['data'])
                        ->usingFileName(uniqid('favicon', false) . '.png')
                        ->toMediaCollection('fav');
                }
            }

            if ($frontendSetting && count($request->logo) > 0) {
                foreach ($request->logo as $image) {
                    $frontendSetting->addMediaFromBase64($image['data'])
                        ->usingFileName(uniqid('logo', false) . '.png')
                        ->toMediaCollection('logo');
                }
            }

            return $this->sendResponse($frontendSetting, 'Frontend general setting updated successfully');

        } catch (\Exception $exception) {
            return $this->sendError('Frontend general setting store error', ['error' => $exception->getMessage()]);
        }
    }
}
