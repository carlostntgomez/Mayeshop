<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DressType;

class DressTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DressType::create(['name' => 'Mini', 'slug' => 'mini']);
        DressType::create(['name' => 'Midi', 'slug' => 'midi']);
        DressType::create(['name' => 'Largo', 'slug' => 'largo']);
        DressType::create(['name' => 'Enterizo', 'slug' => 'enterizo']);
    }
}
