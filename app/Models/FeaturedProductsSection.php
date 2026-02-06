<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedProductsSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'button_text',
        'button_url',
        'sub_banners_data',
        'product_grids_data',
    ];

    protected $casts = [
        'sub_banners_data' => 'array',
        'product_grids_data' => 'array',
    ];

    // This method is a workaround for Filament's Select::make('products')->relationship()
    // when products are stored in a JSON field. It allows Filament to fetch product options.
    public function products()
    {
        return Product::query();
    }
}
