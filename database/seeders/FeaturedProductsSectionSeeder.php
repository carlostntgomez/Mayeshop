<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeaturedProductsSection;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Arr;

class FeaturedProductsSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('featured_products_sections')->truncate();
        Schema::enableForeignKeyConstraints();

        $sampleImagePath = public_path('storage/muetra de imagenes procucto/');
        $specificSubBannerImageName = 'products-sub-banner-1.webp'; // The specific image to use for sub-banners
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

        $processAndStoreImage = function ($sourceImageName, $directory = 'featured-section-images') use ($sampleImagePath) {
            $sourcePath = $sampleImagePath . $sourceImageName;
            $filename = Str::random(40) . '.webp';

            $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($sourcePath);

            // Manual cropping to aspect ratio 330:435 (centered)
            $originalWidth = $image->width();
            $originalHeight = $image->height();
            $targetWidth = 330;
            $targetHeight = 435;
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

            // Resize to exact dimensions 330x435 (upscale or downscale)
            $image->resize($targetWidth, $targetHeight);

            $tempWebpPath = tempnam(sys_get_temp_dir(), 'webp_') . '.webp';
            $image->toWebp(98)->save($tempWebpPath);

            $fullWebpPath = Storage::disk('public')->putFileAs($directory, new \Illuminate\Http\File($tempWebpPath), $filename);

            if (file_exists($tempWebpPath)) {
                unlink($tempWebpPath);
            }
            return $fullWebpPath;
        };

        $products = Product::inRandomOrder()->take(6)->get(); // Get 6 products for 2 grids of 3

        $subBannersData = [];
        for ($i = 0; $i < 2; $i++) {
            $subBannersData[] = [
                'image_path' => $processAndStoreImage($specificSubBannerImageName, 'featured-sub-banners'),
                'title' => '¡Ofertas Exclusivas! ' . ($i + 1),
                'button_text' => 'Comprar Ahora',
                'button_url' => '#',
            ];
        }

        $productGridsData = [];
        $productChunks = $products->chunk(3);
        foreach ($productChunks as $chunk) {
            $productGridsData[] = [
                'products' => $chunk->pluck('id')->toArray(),
            ];
        }

        FeaturedProductsSection::create([
            'title' => 'Descubre lo Nuevo',
            'subtitle' => 'Colección de Verano',
            'button_text' => 'Ver Colección',
            'button_url' => '#',
            'sub_banners_data' => $subBannersData,
            'product_grids_data' => $productGridsData,
        ]);
    }
}