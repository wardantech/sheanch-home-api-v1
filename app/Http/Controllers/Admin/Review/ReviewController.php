<?php

namespace App\Http\Controllers\Admin\Review;

use App\Http\Controllers\Controller;
use App\Models\Review\Review;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ReviewController extends Controller
{

    use ResponseTrait;

    /**
     * Get All Properties Review
     * @param Request $request
     * @return array
     */

    public function getPropertyReviews(Request $request)
    {
        $columns = ['id','review','rating','status'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = Review::with(['tenant' => function ($query) {
            $query->select('id', 'name');
        }, 'property' => function ($query) {
            $query->select('id', 'name', 'user_id')->with(['landlord' => function ($query) {
                $query->select('id', 'name');
            }]);
        }])->where('review_type', 1)
            ->select('id', 'reviewer_type','reviewer_type_id','review_type','review_type_id','review','rating','status')
            ->orderBy($columns[$column], $dir);

        $count = Review::count();
        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('id', 'like', '%' . $searchValue . '%')
                    ->orWhere('reviews', 'like', '%' . $searchValue . '%');
            });
        }

        if($length!='all'){
            $fetchData = $query->paginate($length);
        }
        else{
            $fetchData = $query->paginate($count);
        }

        return ['data' => $fetchData, 'draw' => $request['params']['draw']];
    }

    /**
     * Get All Landlord Review
     * @param Request $request
     * @return array
     */

    public function getLandlordsReviews(Request $request)
    {
        $columns = ['id','review','rating','status'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = Review::with(['tenant' => function ($tenant) {
            $tenant->select('id', 'name');
        }, 'landlord' => function($landlord) {
            $landlord->select('id', 'name');
        }])
        ->where('review_type', 2)
        ->select('id', 'reviewer_type','reviewer_type_id','review_type','review_type_id','review','rating','status')
        ->orderBy($columns[$column], $dir);

        $count = Review::count();
        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('id', 'like', '%' . $searchValue . '%')
                    ->orWhere('reviews', 'like', '%' . $searchValue . '%');
            });
        }

        if($length != 'all'){
            $fetchData = $query->paginate($length);
        }
        else{
            $fetchData = $query->paginate($count);
        }

        return ['data' => $fetchData, 'draw' => $request['params']['draw']];
    }

    /**
     * Get All Landlord Review
     * @param Request $request
     * @return array
     */

    public function getTenantReviews(Request $request)
    {
        $columns = ['id','review','rating','status'];

        $length = $request['params']['length'];
        $column = $request['params']['column'];
        $dir = $request['params']['dir'];
        $searchValue = $request['params']['search'];

        $query = Review::with(['tenant' => function ($tenant) {
            $tenant->select('id', 'name');
        }, 'landlord' => function($landlord) {
            $landlord->select('id', 'name');
        }])
        ->where('review_type', 3)
        ->select('id', 'reviewer_type','reviewer_type_id','review_type','review_type_id','review','rating','status')
        ->orderBy($columns[$column], $dir);

        $count = Review::count();
        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('id', 'like', '%' . $searchValue . '%')
                    ->orWhere('reviews', 'like', '%' . $searchValue . '%');
            });
        }

        if($length != 'all'){
            $fetchData = $query->paginate($length);
        }
        else{
            $fetchData = $query->paginate($count);
        }

        return ['data' => $fetchData, 'draw' => $request['params']['draw']];
    }

    /**
     * Review Data Delete
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        try {
            $review = Review::findOrFail($id);
            $review->delete();

            return $this->sendResponse(['id'=>$id],'Review deleted successfully');
        }catch (\Exception $exception){
            return $this->sendError('Review delete error', ['error' => $exception->getMessage()]);
        }
    }
}
