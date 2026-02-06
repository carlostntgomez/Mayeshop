<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Product;
use Faker\Factory as Faker;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Review::truncate();

        $faker = Faker::create();
        $productIds = Product::pluck('id')->toArray();

        if (empty($productIds)) {
            $this->command->info('No products found. Please seed products first.');
            return;
        }

        $userIds = \App\Models\User::pluck('id')->toArray();

        if (empty($userIds)) {
            $this->command->info('No users found. Please seed users first.');
            return;
        }

        for ($i = 0; $i < 15; $i++) {
            Review::create([
                'product_id' => $faker->randomElement($productIds),
                'reviewer_name' => $faker->name,
                'reviewer_email' => $faker->safeEmail,
                'reviewer_phone' => $faker->phoneNumber,
                'rating' => $faker->numberBetween(1, 5),
                'review_text' => $faker->paragraph,
                'is_approved' => $faker->boolean,
                'is_featured' => $faker->boolean,
            ]);
        }
    }
}