<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HeaderAnnouncement>
 */
class HeaderAnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $icons = [
            'fas fa-truck',
            'fas fa-tag',
            'fas fa-gift',
            'fas fa-star',
        ];

        return [
            'text' => $this->faker->sentence(5),
            'icon' => $this->faker->randomElement($icons),
            'url' => $this->faker->url(),
            'order' => $this->faker->numberBetween(1, 10),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }
}
