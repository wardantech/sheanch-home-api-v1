<?php

namespace App\Http\Controllers\User\Review;

use App\Http\Controllers\Controller;
use App\Models\Review\Review;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use ResponseTrait;

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $review = new Review();
            $review->store($request);
            $review->save();

            $review['tenant']['name'];

            return $this->sendResponse([
                'status' => true,
                'review' => $review
            ], 'Review store successfully');

        }catch (\Exception $exception){
            return $this->sendError('Review Image error', ['error' => $exception->getMessage()]);
        }
    }

    public function getReviews(Request $request)
    {
        try {
            $reviews = Review::where('review_type', 1)
                ->where('review_type_id', $request->propertyId)
                ->where('reviewer_type', 3)
                ->with('tenant', function($query) {
                    $query->select('id', 'name');
                })
                ->latest()
                ->get();

            // $avgRating = $reviews->avg('rating');

            return $this->sendResponse($reviews, 'Get review data successfully.');
        } catch (\Exception $exception) {
            return $this->sendError('There is an error.', ['error' => $exception->getMessage()]);
        }
    }
}
