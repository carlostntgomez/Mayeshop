<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HeaderAnnouncement;

class HeaderAnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HeaderAnnouncement::factory(3)->create();

        $this->command->info('3 Header Announcements created successfully!');
    }
}
