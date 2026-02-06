<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'main_image',
        'image_gallery',
        'price',
        'sale_price',
        'sku',
        'stock',
        'low_stock_threshold',
        'is_featured',
        'status',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'category_id',
        'is_most_selling',
        'product_type_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'image_gallery' => 'array',
        'is_featured' => 'boolean',
        'sale_start_date' => 'datetime',
        'sale_end_date' => 'datetime',
        'product_type_id' => 'integer',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'product_collection');
    }

    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function flashSales(): BelongsToMany
    {
        return $this->belongsToMany(FlashSale::class);
    }

    public function occasions(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tag')->where('type', 'ocasion');
    }

    public function estilos(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tag')->where('type', 'estilo');
    }



    public function temporadas(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tag')->where('type', 'temporada');
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tag')->where('type', 'material');
    }

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->sale_price !== null && $this->sale_price > 0 && $this->sale_price < $this->price) {
            return round((($this->price - $this->sale_price) / $this->price) * 100);
        }

        return 0;
    }

    public function getAverageRatingAttribute()
    {
        $average = $this->reviews()->avg('rating');
        return round($average, 1);
    }
}
