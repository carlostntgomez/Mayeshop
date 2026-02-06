<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'reviewer_name',
        'reviewer_email',
        'reviewer_phone',
        'rating',
        'review_text',
        'is_approved',
        'is_featured',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}