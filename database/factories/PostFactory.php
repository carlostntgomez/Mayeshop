<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(rand(5, 10));
        $content = $this->faker->paragraphs(rand(5, 15), true);
        $excerpt = Str::limit($content, 200);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $content,
            'excerpt' => $excerpt,
            'featured_image' => null, // This will be set in the seeder
            'meta_title' => $this->faker->sentence(rand(3, 7)),
            'meta_description' => $this->faker->paragraph(rand(2, 4)),
            'meta_keywords' => implode(', ', $this->faker->words(rand(3, 7))),
            'status' => $this->faker->randomElement(['draft', 'published', 'archived']),
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'user_id' => User::factory(), // Creates a new user or uses an existing one
        ];
    }

    public function published(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'published',
                'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            ];
        });
    }
}
