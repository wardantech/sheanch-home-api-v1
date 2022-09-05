<?php

namespace App\Http\Controllers\User\Review;

use App\Http\Controllers\Controller;
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
                'status' => true
            ], 'Review store successfully');

        }catch (\Exception $exception){
            return $this->sendError('Review Image error', ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Review\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function show(Review $review)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Review\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Review\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Review\Review  $review
     * @return \Illuminate\Http\Response
     */
    public function destroy(Review $review)
    {
        //
    }
}
