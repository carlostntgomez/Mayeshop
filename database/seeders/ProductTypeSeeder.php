<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Arr;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('product_types')->truncate(); // Use truncate for a full fresh start
        Schema::enableForeignKeyConstraints();

        $sampleImagePath = public_path('storage/muetra de imagenes procucto/');
        $allSampleImages = [
            'Image06.webp', 'Image07.webp', 'Image08.webp', 'Image09.webp', 'Image10.webp',
            'Image11.webp', 'Image12.webp', 'Image13.webp', 'Image14.webp', 'Image15.webp',
            'Image16.webp', 'Image17.webp', 'Image18.webp', 'Image19.webp', 'Image20.webp',
            'Image21.webp', 'Image22.webp', 'Image23.webp', 'Image24.webp', 'Image25.webp',
            'Image26.webp', 'Image27.webp', 'Image28.webp', 'Image29.webp', 'Image30.webp',
            'Image31.webp', 'Image32.webp', 'Image33.webp', 'Image34.webp', 'Image35.webp',
            'Image36.webp', 'Image37.webp', 'Image38.webp', 'Image39.webp', 'Image40.webp',
            'Image41.webp', 'Image42.webp', 'Image43.webp', 'Image44.webp', 'Image45.webp',
            'Image46.webp', 'Image47.webp', 'Image48.webp', 'Image49.webp', 'Image50.webp',
            'Image51.webp', 'Image52.webp', 'Image53.webp', 'Image54.webp', 'Image55.webp',
            'Image56.webp',
        ];

        $processAndStoreImage = function ($sourceImageName, $directory = 'product-type-images') use ($sampleImagePath) {
            $sourcePath = $sampleImagePath . $sourceImageName;
            $filename = Str::random(40) . '.webp';

            $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($sourcePath);

            // Manual cropping to aspect ratio 600:315 (centered)
            $originalWidth = $image->width();
            $originalHeight = $image->height();
            $targetWidth = 600;
            $targetHeight = 315;
            $targetRatio = $targetWidth / $targetHeight;
            $currentRatio = $originalWidth / $originalHeight;

            if ($currentRatio > $targetRatio) {
                // Image is wider than target aspect ratio, crop width
                $newWidth = $originalHeight * $targetRatio;
                $image->crop((int) $newWidth, (int) $originalHeight, (int) (($originalWidth - $newWidth) / 2), 0);
            } elseif ($currentRatio < $targetRatio) {
                // Image is taller than target aspect ratio, crop height
                $newHeight = $originalWidth / $targetRatio;
                $image->crop((int) $originalWidth, (int) $newHeight, 0, (int) (($originalHeight - $newHeight) / 2));
            }

            // Resize to exact dimensions 600x315 (upscale or downscale)
            $image->resize($targetWidth, $targetHeight);

            $tempWebpPath = tempnam(sys_get_temp_dir(), 'webp_') . '.webp';
            $image->toWebp(98)->save($tempWebpPath);

            $fullWebpPath = Storage::disk('public')->putFileAs($directory, new \Illuminate\Http\File($tempWebpPath), $filename);

            if (file_exists($tempWebpPath)) {
                unlink($tempWebpPath);
            }
            return $fullWebpPath;
        };

        $productTypes = [
            [
                'name' => 'Vestidos',
                'slug' => 'vestidos',
                'gender' => 'Mujer',
                'meta_title' => 'Vestidos de Lujo | Maye Shop',
                'meta_description' => 'Descubre la colección exclusiva de vestidos de Maye Shop.',
                'meta_keywords' => 'vestidos, vestidos de lujo, moda femenina',
                'source_image' => 'Image06.webp',
            ],
            [
                'name' => 'Conjuntos (Mujer)',
                'slug' => 'conjuntos-mujer',
                'gender' => 'Mujer',
                'meta_title' => 'Conjuntos de Moda | Maye Shop',
                'meta_description' => 'Explora los conjuntos elegantes y modernos de Maye Shop.',
                'meta_keywords' => 'conjuntos, moda, ropa de mujer',
                'source_image' => 'Image07.webp',
            ],
            [
                'name' => 'Accesorios',
                'slug' => 'accesorios',
                'gender' => 'Unisex',
                'meta_title' => 'Accesorios de Lujo | Maye Shop',
                'meta_description' => 'Complementa tu estilo con los accesorios exclusivos de Maye Shop.',
                'meta_keywords' => 'accesorios, lujo, moda',
                'source_image' => 'Image08.webp',
            ],
            [
                'name' => 'Conjuntos (Hombre)',
                'slug' => 'conjuntos-hombre',
                'gender' => 'Hombre',
                'meta_title' => 'Conjuntos de Hombre | Maye Shop',
                'meta_description' => 'Descubre los conjuntos masculinos de Maye Shop.',
                'meta_keywords' => 'conjuntos hombre, moda masculina, ropa de hombre',
                'source_image' => 'Image09.webp',
            ],
            [
                'name' => 'Pijamas',
                'slug' => 'pijamas',
                'gender' => 'Mujer',
                'meta_title' => 'Pijamas de Lujo | Maye Shop',
                'meta_description' => 'Descansa con estilo con las pijamas exclusivas de Maye Shop.',
                'meta_keywords' => 'pijamas, ropa de dormir, moda femenina',
                'source_image' => 'Image10.webp',
            ],
        ];

        foreach ($productTypes as $productTypeData) {
            $sourceImageName = $productTypeData['source_image']; // Get source image name
            unset($productTypeData['source_image']); // Remove source_image from data to be created

            $imagePath = $processAndStoreImage($sourceImageName, 'product-type-images');
            ProductType::create(array_merge($productTypeData, [
                'image' => $imagePath,
            ]));
        }
    }
}
