<?php

namespace App\Repositories;

use App\Interfaces\GetAddressInterface;
use App\Models\Settings\Division;
use App\Traits\ResponseTrait;


class GetAddressRepository implements GetAddressInterface
{
    use ResponseTrait;

    /**
     * Division list api
     * @return \Illuminate\Http\Response
     */
    public static function getDivisions(): array{
        try{
            $division = Division::all();
            return [
                'status' => true,
                'data' => $division
            ];
        }
        catch (\Exception $exception){
            return [
                'status' => false,
                'data'=> $exception->getMessage()
            ];
        }
    }

    /**
     * District api
     * received divisionId as parameter
     * @return \Illuminate\Http\Response
     */
    public static function getDistricets($divisionId): array{

    }

    /**
     * District api
     * received districtId as parameter
     * @return \Illuminate\Http\Response
     */
    public static function getThanas($districtId): array{

    }


}
