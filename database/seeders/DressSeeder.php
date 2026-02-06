<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Collection;
use App\Models\product;
use App\Models\Tag;
use App\Models\Color;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DressSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Preparar directorios y datos de origen
        $sourceDir = public_path('imagenes de prueba');
        $destinationDir = 'product-images';

        Storage::disk('public')->deleteDirectory($destinationDir);
        Storage::disk('public')->makeDirectory($destinationDir);

        $sourceFiles = collect(File::files($sourceDir));
        $categoryIds = Category::pluck('id')->all();
        $tagIds = Tag::pluck('id')->all();
        $collectionIds = Collection::pluck('id')->all();
        $colorIds = Color::pluck('id')->all();

        if ($sourceFiles->count() < 5 || empty($categoryIds) || empty($tagIds)) {
            $this->command->error('Se necesitan al menos 5 imágenes, y debe haber categorías y tags para ejecutar el seeder.');
            return;
        }

        // --- Generador de Nombres Aleatorios ---
        $styles = ['Vestido', 'Conjunto', 'Enterizo', 'Traje'];
        $adjectives = ['Elegante', 'Atrevido', 'Seductor', 'Nocturno', 'Luminoso', 'Misterioso', 'Urbano', 'Exótico'];
        $nouns = ['Encanto', 'Secreto', 'Deseo', 'Glamour', 'Eclipse', 'Amanecer', 'Ocaso', 'Cosmos'];
        $materials = ['de Seda', 'de Terciopelo', 'con Lentejuelas', 'de Satén', 'de Lycra', 'con Encaje'];

        $generatedNames = [];

        for ($i = 0; $i < 20; $i++) {
            // 2. Generar nombre único y aleatorio
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

            // 3. Seleccionar imágenes aleatorias y únicas para este vestido
            $randomImages = $sourceFiles->random(5);
            $mainImageSource = $randomImages->first();
            $galleryImageSources = $randomImages->slice(1);

            // 4. Copiar y renombrar imágenes basadas en el nuevo slug
            $mainImageExt = $mainImageSource->getExtension();
            $mainImageName = $slug . '-main.' . $mainImageExt;
            $mainImagePath = $destinationDir . '/' . $mainImageName;
            Storage::disk('public')->put($mainImagePath, file_get_contents($mainImageSource->getRealPath()));

            $galleryPaths = [];
            foreach ($galleryImageSources as $key => $galleryFile) {
                $galleryExt = $galleryFile->getExtension();
                $galleryImageName = $slug . '-gallery-' . ($key + 1) . '.' . $galleryExt;
                $galleryImagePath = $destinationDir . '/' . $galleryImageName;
                Storage::disk('public')->put($galleryImagePath, file_get_contents($galleryFile->getRealPath()));
                $galleryPaths[] = $galleryImagePath;
            }

            // 5. Crear el registro del vestido
            $product = product::create([
                'name' => $name,
                'slug' => $slug,
                'description' => "Descubre el {$name}, una pieza de declaración diseñada para la mujer moderna y audaz. Este vestido combina a la perfección la elegancia con un toque de sensualidad, ideal para capturar todas las miradas. Fabricado con materiales de la más alta calidad, su ajuste y caída son impecables.",
                'price' => rand(180000, 650000),
                'sale_price' => rand(0, 1) ? rand(120000, 179000) : null,
                'sku' => 'MAY-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'stock' => rand(3, 25),
                'status' => 'published',
                'is_featured' => rand(0, 1),
                'main_image' => $mainImagePath,
                'main_image_alt' => $name,
                'image_gallery' => json_encode($galleryPaths),
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'meta_title' => "Comprar {$name} | Maye Tienda Online",
                'meta_description' => "Atrévete a lucir espectacular con el {$name}. Calidad, diseño y sensualidad en una sola pieza. Compra online y recíbelo en toda Colombia.",
                'meta_keywords' => str_replace(' ', ', ', strtolower($name)) . ', comprar vestidos, moda colombia',
            ]);

            // 6. Adjuntar relaciones
            $product->tags()->attach(collect($tagIds)->random(rand(4, 7))->all());
            if (rand(0, 2) > 0) {
                $product->collections()->attach(collect($collectionIds)->random(rand(1, 2))->all());
            }
            $product->colors()->attach(collect($colorIds)->random(rand(1, 3))->all());
        }
    }
}