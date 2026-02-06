<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeaderAnnouncement extends Model
{
    use HasFactory;
    protected $fillable = [
        'text',
        'icon',
        'url',
        'order',
        'is_active',
    ];
}
