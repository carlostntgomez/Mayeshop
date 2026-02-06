<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image_path',
        'video_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function ($video) {
            if ($video->is_active) {
                // Set all other records to inactive to ensure only one is active at a time.
                self::where('id', '!=', $video->id)->update(['is_active' => false]);
            }
        });
    }
}