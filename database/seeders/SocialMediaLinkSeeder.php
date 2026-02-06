<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SocialMediaLink;

class SocialMediaLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SocialMediaLink::create([
            'name' => 'Facebook',
            'url' => 'https://www.facebook.com/yourprofile',
            'icon' => 'fab fa-facebook-f',
            'order' => 1,
            'is_active' => true,
        ]);

        SocialMediaLink::create([
            'name' => 'Twitter',
            'url' => 'https://twitter.com/yourprofile',
            'icon' => 'fab fa-twitter',
            'order' => 2,
            'is_active' => true,
        ]);

        SocialMediaLink::create([
            'name' => 'Instagram',
            'url' => 'https://www.instagram.com/yourprofile',
            'icon' => 'fab fa-instagram',
            'order' => 3,
            'is_active' => true,
        ]);

        SocialMediaLink::create([
            'name' => 'YouTube',
            'url' => 'https://www.youtube.com/yourchannel',
            'icon' => 'fab fa-youtube',
            'order' => 4,
            'is_active' => true,
        ]);
    }
}
