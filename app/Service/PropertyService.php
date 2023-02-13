<?php

namespace App\Service;

use App\Models\User;
use App\Models\Settings\Utility;
use App\Models\Settings\Division;
use App\Models\Settings\Facility;
use App\Models\Settings\PropertyType;

class PropertyService
{
    /**
     * Get all property related information.
     *
     * @return array
     */
    public static function getPropertyData(): array
    {
        $users = User::where('is_admin', 0)
                ->select('id', 'name')
                ->where('status', 1)
                ->get();
        $propertyTypes = PropertyType::select('id', 'name')->where('status', 1)->get();
        $division = Division::select('id', 'name')->get();
        $utilities = Utility::select('id', 'name')->where('status', 1)->get();
        $facilities = Facility::select('id', 'name')->where('status', 1)->get();

        return [ $users, $propertyTypes, $division, $utilities, $facilities ];
    }

    /**
     * Get all images form property
     *
     * @param  mixed $medias
     * @return array
     */
    public static function getImages($medias): array
    {
        $propertyImages = [];

        foreach ($medias as $media) {
            $propertyImagesUrl = [];

            $path = $media->getPath();
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:application/' . $type . ';base64,' . base64_encode($data);

            $propertyImagesUrl['url'] = $media->original_url;
            $propertyImagesUrl['data'] = $base64;
            $propertyImagesUrl['size'] = $media->size;
            $propertyImagesUrl['name'] = $media->file_name;

            $propertyImages[] = $propertyImagesUrl;
        }

        return $propertyImages;
    }

    /**
     * Calculate total property rent/sale
     * amount including utilities.
     *
     * @param  mixed $utilities
     * @param  mixed $rent_amount
     * @return float
     */
    public static function totalRentAmount(array $utilities, float $rent_amount): float
    {
        $totalUtility = 0;
        foreach ($utilities as $utility) {
            if ($utility['utility_paid_by'] == 2) {
                $totalUtility += $utility['utility_amount'];
            }
        }

        return $totalUtility + $rent_amount;
    }
}
