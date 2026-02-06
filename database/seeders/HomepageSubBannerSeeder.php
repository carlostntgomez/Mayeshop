<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HomepageSubBanner;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class HomepageSubBannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HomepageSubBanner::truncate();

        $faker = Faker::create();

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

        $processAndStoreImage = function ($sourceImageName, $title, $directory = 'homepage-sub-banners') use ($sampleImagePath) {
            $sourcePath = $sampleImagePath . $sourceImageName;
            $titleSlug = Str::slug($title);
            $filename = "{$titleSlug}-" . Str::random(8) . '.webp';

            $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($sourcePath);

            // Manual cropping to aspect ratio 135:250 (centered)
            $originalWidth = $image->width();
            $originalHeight = $image->height();
            $targetWidth = 135;
            $targetHeight = 250;
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

            // Resize to exact dimensions 135x250 (upscale or downscale)
            $image->resize($targetWidth, $targetHeight);

            $tempWebpPath = tempnam(sys_get_temp_dir(), 'webp_') . '.webp';
            $image->toWebp(98)->save($tempWebpPath);

            $fullWebpPath = Storage::disk('public')->putFileAs($directory, new \Illuminate\Http\File($tempWebpPath), $filename);

            if (file_exists($tempWebpPath)) {
                unlink($tempWebpPath);
            }
            return $fullWebpPath;
        };

        $colors = ['#C8A2A3', '#C0A07C', '#F7F5F2']; // Dusty Rose, Muted Gold/Camel, Soft Off-White

        $bannersData = [
            [
                'title' => 'Elegancia Atemporal',
                'subtitle' => 'Descubre piezas clásicas con un toque moderno',
                'background_color' => '#C8A2A3',
            ],
            [
                'title' => 'Lujo Discreto',
                'subtitle' => 'Calidad y sofisticación en cada detalle',
                'background_color' => '#C0A07C',
            ],
            [
                'title' => 'Colección Exclusiva',
                'subtitle' => 'Diseños únicos para la mujer Maye',
                'background_color' => '#F7F5F2',
            ],
        ];

        foreach ($bannersData as $index => $bannerData) {
            $title = $bannerData['title'];
            $subtitle = $bannerData['subtitle'];
            $background_color = $bannerData['background_color'];
            $link_url = route('shop.index'); // All banners link to the shop page

            $imagePath = $processAndStoreImage(Arr::random($allSampleImages), $title, 'homepage-sub-banners');

            HomepageSubBanner::create([
                'title' => $title,
                'subtitle' => $subtitle,
                'image_path' => $imagePath,
                'link_url' => $link_url,
                'order' => $index,
                'is_active' => true,
                'background_color' => $background_color,
            ]);
        }
    }
}
