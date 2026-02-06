<?php

namespace Database\Factories;

use App\Models\ProductType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->unique()->word();
        $gender = $this->faker->randomElement(['male', 'female', 'unisex']);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'gender' => $gender,
            'meta_title' => 'Meta Title for ' . $name,
            'meta_description' => 'Meta Description for ' . $name,
            'meta_keywords' => 'keywords, for, ' . $name,
            'image' => null, // Assuming image is optional or handled elsewhere
        ];
    }
}
