<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tagsByType = [
            'ocasion' => [
                'Vestidos para fiesta',
                'Vestidos de noche',
                'Vestidos coctel',
                'Vestidos casuales sexies',
                'Vestidos elegantes',
                'Vestidos para grado',
                'Vestidos para cena romántica',
                'Vestidos para conciertos o festivales',
            ],
            'estilo' => [
                'Vestidos con encaje',
                'Vestidos con transparencias',
                'Vestidos con brillos / lentejuelas',
                'Vestidos con espalda descubierta',
                'Vestidos con corte sirena',
                'Vestidos de un solo hombro',
                'Vestidos cut-out (con recortes)',
                'Vestidos con escote profundo',
                'Vestidos con manga larga',
            ],
            'color' => [
                'Negro', 'Rojo', 'Blanco', 'Dorado', 'Plateado', 'Estampado', 'Azul', 'Fucsia', 'Nude',
            ],
            'material' => [
                'Satín / Seda',
                'Lycra / Elastano',
                'Encaje',
                'Tul / Malla',
                'Terciopelo',
                'Lentejuelas',
                'Piel de durazno',
            ],
            'temporada' => [
                'Verano / Playa',
                'Invierno',
                'Urbano Sexy',
                'Clásico',
                'Clima Cálido',
                'Tendencia',
            ],
            'silueta' => [
                'Para figura reloj de arena',
                'Para figura curvilínea',
                'Para figura rectangular',
                'Realza el busto',
                'Disimula el abdomen',
            ],
        ];

        foreach ($tagsByType as $type => $tags) {
            foreach ($tags as $tagName) {
                Tag::firstOrCreate(
                    ['slug' => Str::slug($tagName), 'type' => $type],
                    ['name' => $tagName]
                );
            }
        }
    }
}