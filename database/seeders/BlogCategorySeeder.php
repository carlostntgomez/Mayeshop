<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlogCategory;
use Illuminate\Support\Str;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BlogCategory::truncate();

        $categories = [
            'La Guía de la Invitada (Hecha para Medellín)',
            'Tendencias en Vestidos y Lujo',
            'Manual de Lujo: Vestidos y Materiales',
            'Perfiles de Diseño (El ADN del Lujo)',
        ];

        foreach ($categories as $category) {
            BlogCategory::create([
                'name' => $category,
                'slug' => Str::slug($category),
            ]);
        }
    }
}
