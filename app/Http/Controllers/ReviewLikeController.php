<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewLikeController extends Controller
{
    public function store(Review $review)
    {
        Auth::user()->likedReviews()->syncWithoutDetaching($review->id);
        return back();
    }

    public function destroy(Review $review)
    {
        Auth::user()->likedReviews()->detach($review->id);
        return back();
    }
}