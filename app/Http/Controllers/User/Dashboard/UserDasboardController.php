<?php

namespace App\Http\Controllers\User\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Property\Property;
use App\Models\Property\PropertyAd;
use App\Models\Property\PropertyDeed;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class UserDasboardController extends Controller
{
    use ResponseTrait;

    public function getDashboardData(Request $request)
    {
        try {
            $totalProperties = Property::where('user_id', $request->userId)->where('status',1)->count();
            $totalPoropertyAds = PropertyAd::where('user_id', $request->userId)->where('status',1)->count();
            $totalCompleteDeed = PropertyDeed::where('landlord_id', $request->userId)->where('status',2)->count();

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
