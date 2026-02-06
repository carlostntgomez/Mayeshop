<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FlashSale;
use App\Models\Product;

class FlashSaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Ensure there are products to attach
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->info('Please run the ProductSeeder first to create products.');
            return;
        }

        // Create 1 flash sale using the factory
        FlashSale::factory(1)->create()->each(function ($flashSale) use ($products) {
            // Attach a random number of products (between 1 and 3) to each flash sale
            $flashSale->products()->attach($products->random(rand(1, min(3, $products->count())))->pluck('id'));
        });

        $this->command->info('1 Flash Sale created and products attached successfully!');
    }
}
