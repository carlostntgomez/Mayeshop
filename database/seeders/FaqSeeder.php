<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Faq::create([
            'question' => '¿Cuál es el horario de atención al cliente?',
            'answer' => 'Nuestro equipo de atención al cliente está disponible de lunes a viernes, de 9:00 AM a 6:00 PM (hora local).',
        ]);

        \App\Models\Faq::create([
            'question' => '¿Cuáles son los métodos de pago aceptados?',
            'answer' => 'Aceptamos tarjetas de crédito (Visa, MasterCard, American Express), PayPal y transferencias bancarias.',
        ]);

        \App\Models\Faq::create([
            'question' => '¿Realizan envíos internacionales?',
            'answer' => 'Sí, realizamos envíos a la mayoría de los países. Los costos y tiempos de envío varían según el destino.',
        ]);

        \App\Models\Faq::create([
            'question' => '¿Cómo puedo rastrear mi pedido?',
            'answer' => 'Una vez que tu pedido sea enviado, recibirás un correo electrónico con un número de seguimiento y un enlace para rastrearlo.',
        ]);

        \App\Models\Faq::create([
            'question' => '¿Puedo devolver un producto si no estoy satisfecho?',
            'answer' => 'Sí, aceptamos devoluciones dentro de los 30 días posteriores a la compra, siempre y cuando el producto esté en su estado original y sin usar.',
        ]);
    }
}
