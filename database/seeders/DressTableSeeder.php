<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\product;
use App\Models\Category;
use App\Models\Color;
use App\Models\Tag;
use App\Models\Collection;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class DressTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Get existing categories, colors, tags, and collections
        $categories = Category::all();
        $colors = Color::all();
        $tags = Tag::all();
        $collections = Collection::all();

        // --- Ensure base data exists (from other seeders) ---
        if ($categories->isEmpty()) {
            // Create some default categories if none exist
            $categories = collect([
                Category::create(['name' => 'Vestidos de Noche', 'slug' => 'vestidos-de-noche', 'image' => 'category-images/default-night.webp', 'meta_title' => 'Vestidos de Noche', 'meta_description' => 'Vestidos de Noche', 'meta_keywords' => 'Vestidos de Noche']),
                Category::create(['name' => 'Vestidos Casuales', 'slug' => 'vestidos-casuales', 'image' => 'category-images/default-casual.webp', 'meta_title' => 'Vestidos Casuales', 'meta_description' => 'Vestidos Casuales', 'meta_keywords' => 'Vestidos Casuales']),
                Category::create(['name' => 'Vestidos de Fiesta', 'slug' => 'vestidos-de-fiesta', 'image' => 'category-images/default-party.webp', 'meta_title' => 'Vestidos de Fiesta', 'meta_description' => 'Vestidos de Fiesta', 'meta_keywords' => 'Vestidos de Fiesta']),
            ]);
        }

        if ($colors->isEmpty()) {
            // Create some default colors if none exist
            $colors = collect([
                Color::create(['name' => 'Rojo', 'hex_code' => '#FF0000']),
                Color::create(['name' => 'Azul', 'hex_code' => '#0000FF']),
                Color::create(['name' => 'Verde', 'hex_code' => '#00FF00']),
                Color::create(['name' => 'Negro', 'hex_code' => '#000000']),
                Color::create(['name' => 'Blanco', 'hex_code' => '#FFFFFF']),
            ]);
        }

        // Improved tag seeding based on Tag::TYPES
        if ($tags->isEmpty()) {
            foreach (Tag::TYPES as $typeKey => $typeLabel) {
                // Create 2-3 tags for each type
                for ($j = 0; $j < $faker->numberBetween(2, 3); $j++) {
                    $tagName = $faker->unique()->word() . ' ' . $typeLabel; // Ensure unique name per type
                    Tag::create([
                        'name' => $tagName,
                        'slug' => Str::slug($tagName),
                        'type' => $typeKey,
                    ]);
                }
            }
            // Re-fetch tags after creating them
            $tags = Tag::all();
        }

        if ($collections->isEmpty()) {
            // Create some default collections if none exist
            $collections = collect([
                Collection::create(['name' => 'Novedades', 'slug' => 'novedades', 'description' => 'Descubre lo último en moda.']),
                Collection::create(['name' => 'Más Vendidos', 'slug' => 'mas-vendidos', 'description' => 'Nuestros productos más populares.']),
                Collection::create(['name' => 'Promociones', 'slug' => 'promociones', 'description' => 'Ofertas especiales y descuentos.']),
            ]);
        }

        // --- product Generation --- 
        // Arrays for more realistic name generation
        $styles = ['Vestido', 'Conjunto', 'Enterizo', 'Traje'];
        $adjectives = ['Elegante', 'Atrevido', 'Seductor', 'Nocturno', 'Luminoso', 'Misterioso', 'Urbano', 'Exótico'];
        $nouns = ['Encanto', 'Secreto', 'Deseo', 'Glamour', 'Eclipse', 'Amanecer', 'Ocaso', 'Cosmos'];
        $materials = ['de Seda', 'de Terciopelo', 'con Lentejuelas', 'de Satén', 'de Lycra', 'con Encaje'];

        $generatedNames = [];

        for ($i = 0; $i < 50; $i++) {
            // Generate unique and random name
            do {
                $name = sprintf('%s %s %s %s',
                    $styles[array_rand($styles)],
                    $adjectives[array_rand($adjectives)],
                    $nouns[array_rand($nouns)],
                    $materials[array_rand($materials)]
                );
            } while (in_array($name, $generatedNames));
            $generatedNames[] = $name;
            $slug = Str::slug($name);

            $price = $faker->numberBetween(50000, 500000); // Price between 50,000 and 500,000
            $salePrice = null;
            $isFeatured = $faker->boolean(20); // 20% chance of being featured

            // 50% chance of having a sale price
            if ($faker->boolean(50)) {
                $salePrice = $faker->numberBetween($price * 0.5, $price * 0.9); // Sale price between 50% and 90% of original price
            }

            $product = product::create([
                'name' => $name,
                'slug' => $slug,
                'short_description' => $faker->sentence(10),
                'description' => "Descubre el {$name}, una pieza de declaración diseñada para la mujer moderna y audaz. Este vestido combina a la perfección la elegancia con un toque de sensualidad, ideal para capturar todas las miradas. Fabricado con materiales de la más alta calidad, su ajuste y caída son impecables.",
                'main_image' => 'product-images/' . $faker->uuid() . '.webp', // Using UUID for unique image names
                'image_gallery' => json_encode([]), // Empty image gallery for now
                'price' => $price,
                'sale_price' => $salePrice,
                'sku' => 'MAY-' . $faker->unique()->randomNumber(4),
                'stock' => $faker->numberBetween(0, 100),
                'low_stock_threshold' => $faker->numberBetween(1, 10),
                'is_featured' => $isFeatured,
                'status' => $faker->randomElement(['published', 'draft']),
                'meta_title' => "Comprar {$name} | Maye Tienda Online",
                'meta_description' => "Atrévete a lucir espectacular con el {$name}. Calidad, diseño y sensualidad en una sola pieza. Compra online y recíbelo en toda Colombia.",
                'meta_keywords' => str_replace(' ', ', ', strtolower($name)) . ', comprar vestidos, moda colombia',
                'category_id' => $categories->random()->id,
            ]);

            // Attach random colors (1 to 3 colors)
            $product->colors()->attach(
                $colors->random($faker->numberBetween(1, min(3, $colors->count())))->pluck('id')->toArray()
            );

            // Attach random tags (1 to 3 tags)
            $product->tags()->attach(
                $tags->random($faker->numberBetween(1, min(3, $tags->count())))->pluck('id')->toArray()
            );

            // Attach random collections (0 to 2 collections)
            if ($collections->isNotEmpty() && $faker->boolean(70)) { // 70% chance to attach to collections
                $product->collections()->attach(
                    $collections->random($faker->numberBetween(1, min(2, $collections->count())))->pluck('id')->toArray()
                );
            }
        }
    }
}
