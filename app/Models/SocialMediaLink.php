<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialMediaLink extends Model
{
    protected $fillable = [
        'name',
        'url',
        'icon',
        'order',
        'is_active',
    ];
}
