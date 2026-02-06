<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Arr;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::truncate();

        $faker = Faker::create('es_ES');
        $blogCategoryIds = BlogCategory::pluck('id')->toArray();
        $blogTagIds = BlogTag::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();

        if (empty($blogCategoryIds) || empty($blogTagIds) || empty($userIds)) {
            $this->command->info('Please seed blog categories, tags, and users first.');
            return;
        }

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

        $processAndStoreImage = function ($sourceImageName, $directory = 'posts') use ($sampleImagePath) {
            $sourcePath = $sampleImagePath . $sourceImageName;
            $filename = Str::random(40) . '.webp';

            $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($sourcePath);
            $image->resize(999, 666);

            $tempWebpPath = tempnam(sys_get_temp_dir(), 'webp_') . '.webp';
            $image->toWebp(98)->save($tempWebpPath);

            $fullWebpPath = Storage::disk('public')->putFileAs($directory, new \Illuminate\Http\File($tempWebpPath), $filename);

            if (file_exists($tempWebpPath)) {
                unlink($tempWebpPath);
            }
            return $fullWebpPath;
        };

        $postIdeas = [
            [
                'title' => 'Qué usar para un matrimonio en Zona E: Guía de vestidos de lujo',
                'category' => 'La Guía de la Invitada (Hecha para Medellín)',
                'tags' => ['Invitada de Matrimonio', 'Matrimonio de Noche', 'Vestidos de Lujo', 'Medellín'],
            ],
            [
                'title' => 'Vestidos para los grados de EAFIT: Looks para impactar',
                'category' => 'La Guía de la Invitada (Hecha para Medellín)',
                'tags' => ['Grados', 'Vestidos de Fiesta', 'Medellín', 'EAFIT'],
            ],
            [
                'title' => '5 tendencias en vestidos de fiesta que veremos en Medellín este semestre',
                'category' => 'Tendencias en Vestidos y Lujo',
                'tags' => ['Tendencias 2025', 'Vestidos de Fiesta', 'Moda de Lujo Medellín'],
            ],
            [
                'title' => 'Lujo Silencioso: Cómo llevar la tendencia en la Eterna Primavera',
                'category' => 'Tendencias en Vestidos y Lujo',
                'tags' => ['Lujo Silencioso', 'Eterna Primavera', 'Asesoría de Imagen'],
            ],
            [
                'title' => 'El arte de la seda: Por qué invertir en un vestido de seda y cómo cuidarlo',
                'category' => 'Manual de Lujo: Vestidos y Materiales',
                'tags' => ['Vestidos de Seda', 'Cuidado de Prendas', 'Básicos de Lujo'],
            ],
            [
                'title' => 'Guía de materiales para la Eterna Primavera: Lino, algodón y sedas frías',
                'category' => 'Manual de Lujo: Vestidos y Materiales',
                'tags' => ['Telas de Lujo', 'Eterna Primavera', 'Clima Templado'],
            ],
            [
                'title' => 'Perfil: Diseñador A y el arte de sus vestidos',
                'category' => 'Perfiles de Diseño (El ADN del Lujo)',
                'tags' => ['Diseñador A', 'Diseñadores Colombianos', 'Moda de Lujo Medellín'],
            ],
            [
                'title' => 'La nueva colección de Diseñador B ya está en Medellín',
                'category' => 'Perfiles de Diseño (El ADN del Lujo)',
                'tags' => ['Diseñador B', 'Nuevas Colecciones', 'Tendencias 2025'],
            ],
            [
                'title' => 'El vestido perfecto para una noche en El Poblado',
                'category' => 'La Guía de la Invitada (Hecha para Medellín)',
                'tags' => ['El Poblado', 'Vestidos de Noche', 'Medellín'],
            ],
            [
                'title' => 'Looks para puebliar con estilo: Vestidos de lujo para Guatapé',
                'category' => 'La Guía de la Invitada (Hecha para Medellín)',
                'tags' => ['Puebliar con Estilo', 'Vestidos de Día', 'Guatapé'],
            ],
        ];

        foreach ($postIdeas as $idea) {
            $title = $idea['title'];
            $slug = Str::slug($title);
            $imagePath = $processAndStoreImage(Arr::random($allSampleImages), 'posts');
            $category = BlogCategory::where('name', $idea['category'])->first();
            $tags = BlogTag::whereIn('name', $idea['tags'])->pluck('id');

            $post = Post::create([
                'title' => $title,
                'slug' => $slug,
                'content' => $faker->paragraphs(8, true),
                'excerpt' => $faker->paragraph(3),
                'featured_image' => $imagePath,
                'published_at' => $faker->dateTimeThisYear(),
                'status' => 'published',
                'user_id' => Arr::random($userIds),
                'meta_title' => $title,
                'meta_description' => $faker->sentence(20),
                'meta_keywords' => implode(', ', $idea['tags']),
                'is_featured' => $faker->boolean,
                'blog_category_id' => $category->id,
            ]);

            $post->blogTags()->attach($tags);
        }
    }
}