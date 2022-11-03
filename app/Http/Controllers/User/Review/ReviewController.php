<?php

namespace App\Http\Controllers\User\Review;

use App\Http\Controllers\Controller;
use App\Models\Property\Property;
use App\Models\Review\Review;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

            $review->review = $request->review;
            $review->reviewer_type = $request->reviewer_type;
            $review->review_type = $request->review_type;
            $review->review_type_id = $request->review_type_id;
            $review->reviewer_type_id = $request->reviewer_type_id;
            $review->rating = $request->rating;
            $review->status = $request->status;
            $review->save();

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
            $reviewsTenant = Review::where('review_type', 1)
                ->where('review_type_id', $request->propertyId)
                ->where('reviewer_type', 3)
                ->with('tenant')
                ->latest()
                ->get();

            $reviewsLandlord = Review::where('review_type', 1)
                ->where('review_type_id', $request->propertyId)
                ->where('reviewer_type', 2)
                ->with('landlord')
                ->latest()
                ->get();

            $reviews = $reviewsTenant->merge($reviewsLandlord);

            return $this->sendResponse($reviews, 'Get review data successfully.');
        } catch (\Exception $exception) {
            return $this->sendError('There is an error.', ['error' => $exception->getMessage()]);
        }
    }
}
