<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ColorSetting;

class ColorSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultColors = [
            ['name' => '--black', 'value' => '#2A3B4F'], // Deep Navy
            ['name' => '--white', 'value' => '#FDFBF7'], // Creamy White
            ['name' => '--ul-primary', 'value' => '#C8A2A3'], // Dusty Rose
            ['name' => '--ul-secondary', 'value' => '#C0A07C'], // Muted Gold/Camel
            ['name' => '--ul-tertiary', 'value' => '#F7F5F2'], // Soft Off-White
            ['name' => '--ul-text-gray', 'value' => '#4A3F35'], // Chocolate Brown
        ];

        foreach ($defaultColors as $color) {
            ColorSetting::updateOrCreate(
                ['name' => $color['name']],
                ['value' => $color['value']]
            );
        }
    }
}
