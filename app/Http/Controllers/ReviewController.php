<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'reviewer_name' => 'required|string|max:255',
            'reviewer_email' => 'nullable|email|max:255',
            'reviewer_phone' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'required|string',
            'g-recaptcha-response' => 'required|captcha',
        ]);

        $review = new Review();
        $review->product_id = $product->id;
        $review->reviewer_name = $validated['reviewer_name'];
        $review->reviewer_email = $validated['reviewer_email'] ?? null;
        $review->reviewer_phone = $validated['reviewer_phone'];
        $review->rating = $validated['rating'];
        $review->review_text = $validated['review_text'];
        $review->is_approved = false; // Reviews should be approved by admin before showing
        $review->is_featured = false;
        $review->save();

        return back()->with('success', 'Tu reseña ha sido enviada y está pendiente de aprobación.');
    }
}