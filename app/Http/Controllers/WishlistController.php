<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    use ResponseTrait;

    public function store(Request $request)
    {
        try {
            $tenatId = $request->tenantId;
            $propertyAdId = $request->propertyAdId;


            $check = DB::table('wishlists')->where('tenant_id', $tenatId)
                    ->where('property_ad_id', $propertyAdId)->first();

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
}
