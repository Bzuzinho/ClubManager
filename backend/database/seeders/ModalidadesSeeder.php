<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModalidadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modalidades = [
            [
                'nome' => 'Futebol',
                'codigo' => 'FUT',
                'descricao' => 'Futebol de 11',
                'icone' => 'soccer',
                'cor' => '#2ecc71',
                'ativa' => true,
                'ordem' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Futsal',
                'codigo' => 'FUTS',
                'descricao' => 'Futebol de salão',
                'icone' => 'futsal',
                'cor' => '#3498db',
                'ativa' => true,
                'ordem' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Basquetebol',
                'codigo' => 'BASQ',
                'descricao' => 'Basquetebol',
                'icone' => 'basketball',
                'cor' => '#e74c3c',
                'ativa' => true,
                'ordem' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('modalidades')->insert($modalidades);
    }
}
