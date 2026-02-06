<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Setting::updateOrCreate(
            ['key' => 'GEMINI_API_KEY'],
            ['value' => 'AIzaSyAghySAwYZ1y97_OGhmUKmuSMbqwLipUro']
        );
        Setting::updateOrCreate(
            ['key' => 'whatsapp_phone_number'],
            ['value' => '3123181506']
        );
        Schema::enableForeignKeyConstraints();
    }
}
