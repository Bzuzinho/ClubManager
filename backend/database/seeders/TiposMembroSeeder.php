<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposMembroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            [
                'nome' => 'Atleta',
                'codigo' => 'ATLETA',
                'descricao' => 'Membro praticante de modalidades desportivas',
                'mensalidade' => 25.00,
                'limite_modalidades' => 1,
                'requer_encarregado' => true,
                'pode_competir' => true,
                'ativo' => true,
                'ordem' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Sócio',
                'codigo' => 'SOCIO',
                'descricao' => 'Sócio do clube sem prática desportiva',
                'mensalidade' => 10.00,
                'limite_modalidades' => 0,
                'requer_encarregado' => false,
                'pode_competir' => false,
                'ativo' => true,
                'ordem' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Encarregado',
                'codigo' => 'ENCARREGADO',
                'descricao' => 'Encarregado de educação de atleta',
                'mensalidade' => 0.00,
                'limite_modalidades' => 0,
                'requer_encarregado' => false,
                'pode_competir' => false,
                'ativo' => true,
                'ordem' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Staff',
                'codigo' => 'STAFF',
                'descricao' => 'Treinador, dirigente ou funcionário',
                'mensalidade' => 0.00,
                'limite_modalidades' => 0,
                'requer_encarregado' => false,
                'pode_competir' => false,
                'ativo' => true,
                'ordem' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('tipos_membro')->insert($tipos);
    }
}
