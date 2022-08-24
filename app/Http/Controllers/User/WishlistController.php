<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    use ResponseTrait;

    public function getLists(Request $request)
    {
        $columns = ['id', 'property_ad_id', 'tenant_id'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = Wishlist::with([
            'propertyAd' => function ($query) {
                $query->with(['property' => function ($q) {
                    $q->select('id', 'name');
                }]);
            }
        ])
            ->where('tenant_id', $request->tenantId)
            ->select('id', 'property_ad_id', 'tenant_id')
            ->orderBy($columns[$column], $dir);

        $count = Wishlist::count();
        if ($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('id', 'like', '%' . $searchValue . '%')
                    ->orWhere('property_ad_id', 'like', '%' . $searchValue . '%');
            });
        }

        if ($length != 'all') {
            $fetchData = $query->paginate($length);
        } else {
            $fetchData = $query->paginate($count);
        }

        return ['data' => $fetchData, 'draw' => $request['params']['draw']];
    }

    /**
     * Store wishlist
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        try {
            $tenatId = $request->tenantId;
            $propertyAdId = $request->propertyAdId;

            $check = DB::table('wishlists')->where('tenant_id', $tenatId)
                ->where('property_ad_id', $propertyAdId)
                ->first() ? true : false;

            if($check) {
                return $this->sendResponse([
                    'status' => false
                ], 'Property Already Has on your wishlist');
            }else {
                $wishlist = new Wishlist();

                $wishlist->property_ad_id = $propertyAdId;
                $wishlist->tenant_id = $tenatId;
                $wishlist->save();

                return $this->sendResponse([
                    'status' => true
                ], 'Successfully added wishlist.');
            }

        }catch (\Exception $exception){
            return $this->sendError('Wishlist Image error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Tenant Data Delete
     * @param $id
     * @return mixed
     */

    public function destroy(Request $request)
    {
        try {
            $id = $request->wishlistId;

            $check = DB::table('wishlists')
                    ->where('tenant_id', $request->tenantId)
                    ->first() ? true : false;

            if($check) {
                $wishlist = Wishlist::findOrFail($id);
                $wishlist->delete();
                return $this->sendResponse(['id'=>$id],'Wishlist deleted successfully');
            }

            return $this->sendError('Warning','Something is wrong!');
        }catch (\Exception $exception){
            return $this->sendError('Wishlist delete error', ['error' => $exception->getMessage()]);
        }
    }
}
