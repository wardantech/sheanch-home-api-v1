<?php

namespace App\Http\Controllers\Admin\Wishlists;

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
            'propertyAd' => function ($adQuery) {
                $adQuery->with(['property' => function ($q) {
                    $q->select('id', 'name');
                }]);
            },
            'tenant' => function ($tenantQuery) {
                $tenantQuery->select('id', 'name');
            }
        ])
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
     * Tenant Data Delete
     * @param $id
     * @return mixed
     */

    public function destroy($id)
    {
        try {
            $wishlist = Wishlist::findOrFail($id);
            $wishlist->delete();

            return $this->sendResponse(['id'=>$id],'Wishlist deleted successfully');
        }catch (\Exception $exception){
            return $this->sendError('Wishlist delete error', ['error' => $exception->getMessage()]);
        }
    }
}
