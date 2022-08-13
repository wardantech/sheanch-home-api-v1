<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;

use App\Models\Property\Property;
use App\Models\Property\PropertyAd;
use App\Models\Property\PropertyDeed;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    use ResponseTrait;

    public function getDashbordData(){
        try {
            $totalProperties = Property::where('status',1)->count();
            $totalPoropertyAds = PropertyAd::where('status',1)->count();
            $totalCompleteDeed = PropertyDeed::where('status',2)
            ->count();

            return $this->sendResponse([
                'totalProperties' => $totalProperties,
                'totalPropertyAds' => $totalPoropertyAds,
                'totalCompleteDeed' => $totalCompleteDeed,

            ],'Dashboard data get successfully');
        }catch (\Exception $exception){
            return $this->sendError('Dashboard data get error', ['error' => $exception->getMessage()]);
        }
    }


}
