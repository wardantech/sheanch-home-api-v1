<?php

namespace App\Service;

use App\Traits\ResponseTrait;


class FrontendSettings
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
