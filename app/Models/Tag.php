<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
    ];

    public const TYPES = [
        'ocasion' => 'Ocasión',
        'estilo' => 'Estilo / Diseño',
        'color' => 'Color',
        'material' => 'Material',
        'temporada' => 'Temporada / Tendencia',
        'silueta' => 'Silueta / Tipo de Cuerpo',
        'other' => 'Otro',
    ];

    public static function getTypes(): array
    {
        return self::TYPES;
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}