<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'price_text',
        'button_text',
        'button_url',
        'image_path',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($banner) {
            if ($banner->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }
        });
    }
}
