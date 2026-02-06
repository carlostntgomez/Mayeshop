<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AboutPageContent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AboutPageContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staticImagePath = public_path('static/picture/');
        $destinationDirectory = 'about-page'; // Relative to public disk root

        $processAndStoreImage = function ($sourceImageName, $width, $height) use ($staticImagePath, $destinationDirectory) {
            $sourcePath = $staticImagePath . $sourceImageName;
            $filename = Str::random(8) . '.webp';

            $manager = new ImageManager(new Driver());
            $image = $manager->read($sourcePath);

            $image->cover($width, $height);

            $tempWebpPath = tempnam(sys_get_temp_dir(), 'webp_') . '.webp';
            $image->toWebp(90)->save($tempWebpPath);

            $fullWebpPath = Storage::disk('public')->putFileAs($destinationDirectory, new \Illuminate\Http\File($tempWebpPath), $filename);

            if (file_exists($tempWebpPath)) {
                unlink($tempWebpPath);
            }
            return $fullWebpPath;
        };

        // Clear existing content to avoid duplicates if seeder is run multiple times
        AboutPageContent::truncate();

        $coverImage = $processAndStoreImage('about-cover-img.jpg', 900, 300);
        $section1Image = $processAndStoreImage('about-img-1.jpg', 1024, 1024);
        $section2Image = $processAndStoreImage('about-img-2.jpg', 1024, 1024);

        AboutPageContent::create([
            'breadcrumb_title' => 'Acerca de Nosotros',
            'cover_image' => $coverImage,
            'section1_subtitle' => 'Sobre Nosotros',
            'section1_title' => 'Somos gente glamurosa',
            'section1_paragraph' => 'Vestibulum quis lobortis mauris. Donec molestie porta nibh quis tristique. Vivamus pharetra pretium augue a tempus. Nunc eu lorem quis ex vestibulum dignissim accumsan id velit. Pellentesque pretium, mi in posuere euismod, nulla dolor blandit purus, a eleifend velit massa quis nisi. Integer gravida dictum ipsum ac fringilla. Sed non neque est. Fusce faucibus velit ac volutpat faucibus. In sapien tellus, viverra vitae elementum eu, hendrerit id eros. Duis libero turpis, elementum non molestie ornare, dictum et odio. Quisque dui dolor, commodo in malesuada id, porttitor in enim. Suspendisse elementum ante at venenatis tristique. Nam non ex porta, aliquam tellus vitae, vulputate mauris.',
            'section1_image' => $section1Image,
            'section2_subtitle' => 'Nuestra historia',
            'section2_title' => 'Establecidos - 1995',
            'section2_paragraph' => 'Vestibulum quis lobortis mauris. Donec molestie porta nibh quis tristique. Vivamus pharetra pretium augue a tempus. Nunc eu lorem quis ex vestibulum dignissim accumsan id velit. Pellentesque pretium, mi in posuere euismod, nulla dolor blandit purus, a eleifend velit massa quis nisi. Integer gravida dictum ipsum ac fringilla. Sed non neque est. Fusce faucibus velit ac volutpat faucibus. In sapien tellus, viverra vitae elementum eu, hendrerit id eros. Duis libero turpis, elementum non molestie ornare, dictum et odio. Quisque dui dolor, commodo in malesuada id, porttitor in enim. Suspendisse elementum ante at venenatis tristique. Nam non ex porta, aliquam tellus vitae, vulputate mauris.',
            'section2_image' => $section2Image,
            'more_about_heading_title' => 'La calidad es nuestra prioridad',
            'more_about_heading_description' => 'Nuestros talentosos estilistas han creado atuendos perfectos para la temporada. Tienen una variedad de formas para inspirar tu próximo look a la moda.',
            'point1_title' => 'Diseño de Tendencia',
            'point1_description' => 'Vestibulum quis lobortis mauris. Donec molestie porta nibh quis tristique. Vivamus pharetra pretium augue a tempus. Nunc eu lorem quis ex vestibulum dignissim accumsan id velit.',
            'point2_title' => 'Múltiples Tallas',
            'point2_description' => 'Vestibulum quis lobortis mauris. Donec molestie porta nibh quis tristique. Vivamus pharetra pretium augue a tempus. Nunc eu lorem quis ex vestibulum dignissim accumsan id velit.',
            'point3_title' => 'La Alta Calidad Importa',
            'point3_description' => 'Vestibulum quis lobortis mauris. Donec molestie porta nibh quis tristique. Vivamus pharetra pretium augue a tempus. Nunc eu lorem quis ex vestibulum dignissim accumsan id velit.',
        ]);
    }
}