<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductType;
use App\Models\Color;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\File;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        // Asegurarse de que existan categorías, tipos de producto, colores y tags
        $categories = Category::all();
        $productTypes = ProductType::all();
        $colors = Color::all();
        $tags = Tag::all();

        if ($categories->isEmpty() || $productTypes->isEmpty() || $colors->isEmpty() || $tags->isEmpty()) {
            $this->command->info('Please run seeders for Category, ProductType, Color, and Tag first.');
            return;
        }

        // Crear 3 productos usando el factory
        Product::factory(5)->create()->each(function ($product) use ($categories, $colors, $productTypes, $faker) {
            // Obtener tags filtrados por tipo
            $ocasionTags = Tag::where('type', 'ocasion')->get();
            $estiloTags = Tag::where('type', 'estilo')->get();
            $temporadaTags = Tag::where('type', 'temporada')->get();
            $materialTags = Tag::where('type', 'material')->get();

            // Asignar una categoría aleatoria
            $product->category_id = $categories->random()->id;

            // La image_gallery ya es manejada por ProductFactory.
            // No es necesario generar imágenes de galería aquí.

            $product->save();

            // Adjuntar colores aleatorios (entre 1 y 3)
            $product->colors()->syncWithoutDetaching($colors->random(rand(1, 3))->pluck('id'));

            // Adjuntar tags aleatorios de cada tipo a las relaciones correspondientes
            if ($ocasionTags->isNotEmpty()) {
                $product->occasions()->syncWithoutDetaching($ocasionTags->random(rand(0, min(3, $ocasionTags->count())))->pluck('id'));
            }
            if ($estiloTags->isNotEmpty()) {
                $product->estilos()->syncWithoutDetaching($estiloTags->random(rand(0, min(3, $estiloTags->count())))->pluck('id'));
            }
            if ($temporadaTags->isNotEmpty()) {
                $product->temporadas()->syncWithoutDetaching($temporadaTags->random(rand(0, min(3, $temporadaTags->count())))->pluck('id'));
            }
            if ($materialTags->isNotEmpty()) {
                $product->materials()->syncWithoutDetaching($materialTags->random(rand(0, min(3, $materialTags->count())))->pluck('id'));
            }
        });

        $this->command->info('5 products with image galleries created successfully!');
    }
}