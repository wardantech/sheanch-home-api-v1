<?php

namespace App\Service;
class FrontendSettingsServices
{

    /**
     * Banner Image delete
     * @return \Illuminate\Http\Response
     */

    public static function imageDelete($data, $type){
        if($type == 'banner'){
            $images = $data->getMedia('banner');
        }
        elseif ($type == 'favicon'){
            $images = $data->getMedia('favicon');
        }
        elseif($type == 'logo'){
            $images = $data->getMedia('logo');
        }
        elseif($type == 'footerLogo'){
            $images = $data->getMedia('footerLogo');
        }

        if(count($images) > 0){
            foreach ($images as $image) {
                $image->delete();
            }
        }
    }


    public static function imageUpload($data, $image, $type){
        $data->addMediaFromBase64($image)
            ->usingFileName(uniqid($type, false) . '.png')
            ->toMediaCollection($type);
    }


}
