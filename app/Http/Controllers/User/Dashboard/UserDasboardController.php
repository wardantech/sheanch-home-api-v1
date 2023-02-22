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
            $columns = ['id', 'name'];
            $length = $request['params']['length'];
            $column = $request['params']['column'];
            $dir = $request['params']['dir'];
            $searchValue = $request['params']['search'];
            $userId = $request['params']['userId'];

            $totalProperties = Property::where('user_id', $userId)->where('status',1)->count();
            $totalPoropertyAds = PropertyAd::where('user_id', $userId)->where('status',1)->count();
            $totalCompleteDeed = PropertyDeed::where('landlord_id', $userId)->where('status',2)->count();

            $query = PropertyDeed::with(['tenant' => function($query){
                $query->select('id', 'name');
            }, 'property' => function($query){
                $query->select('id', 'name');
            }])
            ->where('status', 5)
            ->where('landlord_id', $userId)
            ->orderBy($columns[$column], $dir);

            $count = PropertyDeed::count();

            if ($searchValue) {
                $query->where(function ($query) use ($searchValue) {
                    $query->where('id', 'like', '%' . $searchValue . '%')
                        ->orWhere('name', 'like', '%' . $searchValue . '%');
                });
            }

            if ($length != 'all') {
                $fetchData = $query->paginate($length);
            } else {
                $fetchData = $query->paginate($count);
            }

            return [
                'data' => $fetchData,
                'draw' => $request['params']['draw'],
                'totalProperties' => $totalProperties,
                'totalPoropertyAds' => $totalPoropertyAds,
                'totalCompleteDeed' => $totalCompleteDeed,
            ];
        }catch (\Exception $exception){
            return $this->sendError('Dashboard data get error', ['error' => $exception->getMessage()]);
        }
    }
}
