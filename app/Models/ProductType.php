<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'gender',
        'image',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    public function categories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Category::class, 'category_type_id');
    }

    public function products(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Product::class, Category::class, 'category_type_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($productType) {
            if ($productType->image) {
                Storage::disk('public')->delete($productType->image);
            }
        });
    }
}