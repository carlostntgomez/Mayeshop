<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlogTag;
use Illuminate\Support\Str;

class BlogTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BlogTag::truncate();

        $tags = [
            // Por Producto
            'Vestidos', 'Vestidos de Fiesta', 'Vestidos Largos', 'Vestidos Cortos', 'Vestidos Midi', 'Vestidos de Lino', 'Vestidos de Seda', 'Vestidos Estampados', 'Little Black Dress', 'Vestidos de Noche', 'Vestidos de Día', 'Conjuntos de Lujo',
            // Por Ocasión
            'Invitada de Matrimonio', 'Matrimonio de Día', 'Matrimonio de Noche', 'Grados', 'Bautizo', 'Primera Comunión', 'Evento de Gala', 'Coctel', 'Look de Oficina', 'Cena Romántica', 'Almuerzo Elegante',
            // Por Contexto
            'Medellín', 'Eterna Primavera', 'El Poblado', 'Provenza', 'Llano Grande', 'Puebliar con Estilo', 'Colombiamoda', 'Clima Templado',
            // Por Concepto y Estilo
            'Asesoría de Imagen', 'Cuidado de Prendas', 'Lujo Silencioso', 'Básicos de Lujo', 'Cómo Combinar', 'Looks Monocromáticos', 'Tendencias 2025',
            // Por Diseñador (ejemplos)
            'Diseñador A', 'Diseñador B', 'Diseñadores Colombianos',
        ];

        foreach ($tags as $tag) {
            BlogTag::create([
                'name' => $tag,
                'slug' => Str::slug($tag),
            ]);
        }
    }
}
