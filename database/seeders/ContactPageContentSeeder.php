<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactPageContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\ContactPageContent::create([
            'breadcrumb_title' => 'Contáctanos',
            'heading_title' => 'Ponte en Contacto',
            'heading_description' => '¿Tienes alguna pregunta o necesitas ayuda? No dudes en contactarnos. Estamos aquí para ayudarte.',
            'address' => '123 Calle Principal, Ciudad, País',
            'phone' => '+123 456 7890',
            'email' => 'info@mayeshop.com',
            'map_embed_code' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d8684.030842913655!2d90.36627512368048!3d23.776418440774698!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b8f3f608843b%3A0xf2c71ff392721324!2sLiberation%20War%20Museum!5e0!3m2!1sen!2sbd!4v1730028096808!5m2!1sen!2sbd" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
        ]);
    }
}
