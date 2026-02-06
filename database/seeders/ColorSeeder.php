<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Color;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('colors')->truncate();
        Schema::enableForeignKeyConstraints();

        $colors = [
            ['name' => 'Negro', 'hex_code' => '#000000'],
            ['name' => 'Blanco', 'hex_code' => '#FFFFFF'],
            ['name' => 'Gris', 'hex_code' => '#808080'],
            ['name' => 'Beige / Nude', 'hex_code' => '#D8BBAA'],
            ['name' => 'Marrón / Café', 'hex_code' => '#8B4513'],
            ['name' => 'Rojo', 'hex_code' => '#FF0000'],
            ['name' => 'Azul', 'hex_code' => '#0000FF'],
            ['name' => 'Verde', 'hex_code' => '#008000'],
            ['name' => 'Amarillo', 'hex_code' => '#FFFF00'],
            ['name' => 'Naranja', 'hex_code' => '#FFA500'],
            ['name' => 'Rosa', 'hex_code' => '#FFC0CB'],
            ['name' => 'Morado / Violeta', 'hex_code' => '#8A2BE2'],
            ['name' => 'Dorado', 'hex_code' => '#FFD700'],
            ['name' => 'Plateado', 'hex_code' => '#C0C0C0'],
            ['name' => 'Estampado / Multicolor', 'hex_code' => '#BDBDBD'],
        ];

        foreach ($colors as $color) {
            Color::create($color);
        }
    }
}
