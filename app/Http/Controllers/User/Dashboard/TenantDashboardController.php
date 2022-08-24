<?php

namespace App\Http\Controllers\User\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Property\Property;
use App\Models\Property\PropertyAd;
use App\Models\Property\PropertyDeed;
use App\Models\Wishlist;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class TenantDashboardController extends Controller
{
    use ResponseTrait;

    public function getDashboardData(Request $request)
    {
        try {

            $totalRequestDeed = PropertyDeed::where('tenant_id', $request->tenantId)->where('status', 0)
                ->count();
            $totalCompleteDeed = PropertyDeed::where('tenant_id', $request->tenantId)->where('status', 2)
                ->count();
            $wishlistCount = Wishlist::where('tenant_id', $request->tenantId)->count();


            return $this->sendResponse([
                'totalRequestDeed' => $totalRequestDeed,
                'totalCompleteDeed' => $totalCompleteDeed,
                'wishlistCount' => $wishlistCount,

            ],'Dashboard data get successfully');
        }catch (\Exception $exception){
            return $this->sendError('Dashboard data get error', ['error' => $exception->getMessage()]);
        }
    }
}
