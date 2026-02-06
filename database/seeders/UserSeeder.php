<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@maye.com'],
            [
                'name' => 'Admin Maye',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}
