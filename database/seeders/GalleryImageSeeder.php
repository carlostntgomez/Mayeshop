<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GalleryImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Arr;

class GalleryImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('gallery_images')->truncate();
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

        $processAndStoreImage = function ($sourceImageName, $directory = 'gallery-images') use ($sampleImagePath) {
            $sourcePath = $sampleImagePath . $sourceImageName;
            $filename = Str::random(40) . '.webp';

            $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($sourcePath);

            // Manual cropping to aspect ratio 506:506 (centered)
            $originalWidth = $image->width();
            $originalHeight = $image->height();
            $targetWidth = 506;
            $targetHeight = 506;
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

            // Resize to exact dimensions 506x506 (upscale or downscale)
            $image->resize($targetWidth, $targetHeight);

            $tempWebpPath = tempnam(sys_get_temp_dir(), 'webp_') . '.webp';
            $image->toWebp(98)->save($tempWebpPath);

            $fullWebpPath = Storage::disk('public')->putFileAs($directory, new \Illuminate\Http\File($tempWebpPath), $filename);

            if (file_exists($tempWebpPath)) {
                unlink($tempWebpPath);
            }
            return $fullWebpPath;
        };

        $galleryImagesData = [
            [
                'alt_text' => 'Colección de Noche Elegante',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'alt_text' => 'Vestido de Fiesta Exclusivo',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'alt_text' => 'Moda Femenina Medellín',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'alt_text' => 'Accesorios de Lujo',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'alt_text' => 'Tendencias de Temporada',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'alt_text' => 'Estilo Urbano Chic',
                'order' => 6,
                'is_active' => true,
            ],
            [
                'alt_text' => 'Diseño Colombiano',
                'order' => 7,
                'is_active' => true,
            ],
            [
                'alt_text' => 'Glamour y Sofisticación',
                'order' => 8,
                'is_active' => true,
            ],
            [
                'alt_text' => 'Inspiración de Moda',
                'order' => 9,
                'is_active' => true,
            ],
            [
                'alt_text' => 'Novedades Maye Shop',
                'order' => 10,
                'is_active' => true,
            ],
        ];

        $productIds = \App\Models\Product::pluck('id')->toArray();

        if (empty($productIds)) {
            $this->command->info('No products found. Please seed products first.');
            return;
        }

        foreach ($galleryImagesData as $imageData) {
            $imagePath = $processAndStoreImage(Arr::random($allSampleImages), 'gallery-images');
            GalleryImage::create(array_merge($imageData, [
                'product_id' => Arr::random($productIds), // Assign a random product_id
                'image_path' => $imagePath,
            ]));
        }
    }
}
