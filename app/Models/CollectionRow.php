<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionRow extends Model
{
    use HasFactory;

    protected $fillable = [
        'collection_id',
        'sub_banner_image',
        'sub_banner_title',
        'sub_banner_url',
        'order',
    ];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }
}