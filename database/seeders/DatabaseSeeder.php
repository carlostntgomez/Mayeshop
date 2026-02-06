<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            ColorSeeder::class,
            UserSeeder::class,
            TagSeeder::class,
            SettingSeeder::class,
            ProductTypeSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            BannerSeeder::class,
            GalleryImageSeeder::class,
            VideoSeeder::class,
            FeaturedProductsSectionSeeder::class,
            ReviewSeeder::class,
            HomepageSubBannerSeeder::class,
            BlogCategorySeeder::class,
            BlogTagSeeder::class,
            PostSeeder::class,
            ColorSettingSeeder::class,
            FlashSaleSeeder::class,
            HeaderAnnouncementSeeder::class,
            AboutPageContentSeeder::class,
        ]);
    }
}
