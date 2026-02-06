<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\Category;
use App\Models\Color;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $productType = ProductType::inRandomOrder()->first() ?? ProductType::factory()->create();

        // Ensure a category exists for the chosen product type
        $category = Category::where('category_type_id', $productType->id)->inRandomOrder()->first();

        if (!$category) {
            // If no category exists for this product type, create one
            $category = Category::factory()->create([
                'category_type_id' => $productType->id,
                'name' => $this->faker->unique()->word(), // Ensure unique name
                'slug' => $this->faker->unique()->slug(), // Ensure unique slug
            ]);
        }

        $name = $this->faker->words(3, true);
        $description = $this->faker->paragraphs(3, true);
        $shortDescription = $this->faker->sentence();

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

        $name = $this->faker->words(3, true);

        $processAndStoreImage = function ($sourceImageName, $directory) use ($sampleImagePath, $name) {
            $sourcePath = $sampleImagePath . $sourceImageName;
            $nameSlug = Str::slug($name);
            $filename = "{$nameSlug}-gallery-" . Str::random(8) . '.webp';

            $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($sourcePath);
            $image->resize(1024, 1024, function ($constraint) {
                $constraint->aspectRatio();
            });

            $tempWebpPath = tempnam(sys_get_temp_dir(), 'webp_') . '.webp';
            $image->toWebp(98)->save($tempWebpPath);

            $fullWebpPath = Storage::disk('public')->putFileAs($directory, new \Illuminate\Http\File($tempWebpPath), $filename);

            if (file_exists($tempWebpPath)) {
                unlink($tempWebpPath);
            }
            return $fullWebpPath;
        };

        $mainImage = $processAndStoreImage($this->faker->randomElement($allSampleImages), 'product-main-images');

        $galleryImages = [];
        for ($i = 0; $i < 2; $i++) {
            $path = $processAndStoreImage($this->faker->randomElement($allSampleImages), 'product-gallery-images');
            $galleryImages[] = str_replace('\\', '/', $path);
        }

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'sku' => $this->faker->unique()->ean13(),
            'description' => $description,
            'short_description' => $shortDescription,
            'price' => $this->faker->numberBetween(50000, 500000),
            'sale_price' => $this->faker->boolean(30) ? $this->faker->numberBetween(30000, 450000) : null,
            'stock' => $this->faker->numberBetween(0, 100),
            'low_stock_threshold' => $this->faker->numberBetween(1, 5),
            'is_featured' => $this->faker->boolean(20),
            'status' => $this->faker->randomElement(['published', 'draft']),
            'main_image' => $mainImage,
            'image_gallery' => $galleryImages,
            'seo_title' => 'Comprar ' . $name . ' | Maye Shop',
            'seo_description' => Str::limit($description, 150),
            'seo_keywords' => implode(', ', $this->faker->words(5)),
            'category_id' => $category->id,
            'product_type_id' => $productType->id,
            'average_rating' => $this->faker->randomFloat(1, 3, 5),
            'is_most_selling' => $this->faker->boolean(10),
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            // Attach Colors
            $colors = Color::inRandomOrder()->limit($this->faker->numberBetween(1, 3))->get();
            $product->colors()->sync($colors->pluck('id'));

            // Attach Tags
            $ocasionTags = Tag::where('type', 'ocasion')->inRandomOrder()->limit($this->faker->numberBetween(1, 2))->get();
            $product->tags()->syncWithoutDetaching($ocasionTags->pluck('id'));

            $estiloTags = Tag::where('type', 'estilo')->inRandomOrder()->limit($this->faker->numberBetween(1, 2))->get();
            $product->tags()->syncWithoutDetaching($estiloTags->pluck('id'));

            $temporadaTags = Tag::where('type', 'temporada')->inRandomOrder()->limit($this->faker->numberBetween(1, 2))->get();
            $product->tags()->syncWithoutDetaching($temporadaTags->pluck('id'));

            $materialTags = Tag::where('type', 'material')->inRandomOrder()->limit($this->faker->numberBetween(1, 2))->get();
            $product->tags()->syncWithoutDetaching($materialTags->pluck('id'));
        });
    }
}
