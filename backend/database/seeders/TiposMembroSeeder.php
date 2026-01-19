<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoMembro;

class TiposMembroSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['slug' => 'atleta', 'descricao' => 'Atleta'],
            ['slug' => 'encarregado_educacao', 'descricao' => 'Encarregado de Educação'],
            ['slug' => 'treinador', 'descricao' => 'Treinador'],
            ['slug' => 'dirigente', 'descricao' => 'Dirigente'],
            ['slug' => 'voluntario', 'descricao' => 'Voluntário'],
        ];

        foreach ($tipos as $tipo) {
            TipoMembro::firstOrCreate(
                ['slug' => $tipo['slug']],
                $tipo
            );
        }
    }
}
