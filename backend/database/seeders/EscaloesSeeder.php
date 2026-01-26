<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EscaloesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $escaloes = [
            ['nome' => 'Petizes', 'codigo' => 'PET', 'idade_minima' => 5, 'idade_maxima' => 7, 'ano_nascimento_inicio' => 2019, 'ano_nascimento_fim' => 2021, 'genero' => 'misto', 'ativo' => true, 'ordem' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Traquinas', 'codigo' => 'TRA', 'idade_minima' => 8, 'idade_maxima' => 9, 'ano_nascimento_inicio' => 2017, 'ano_nascimento_fim' => 2018, 'genero' => 'misto', 'ativo' => true, 'ordem' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Benjamins', 'codigo' => 'BEN', 'idade_minima' => 10, 'idade_maxima' => 11, 'ano_nascimento_inicio' => 2015, 'ano_nascimento_fim' => 2016, 'genero' => 'misto', 'ativo' => true, 'ordem' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Infantis', 'codigo' => 'INF', 'idade_minima' => 12, 'idade_maxima' => 13, 'ano_nascimento_inicio' => 2013, 'ano_nascimento_fim' => 2014, 'genero' => 'misto', 'ativo' => true, 'ordem' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Iniciados', 'codigo' => 'INI', 'idade_minima' => 14, 'idade_maxima' => 15, 'ano_nascimento_inicio' => 2011, 'ano_nascimento_fim' => 2012, 'genero' => 'misto', 'ativo' => true, 'ordem' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Juvenis', 'codigo' => 'JUV', 'idade_minima' => 16, 'idade_maxima' => 17, 'ano_nascimento_inicio' => 2009, 'ano_nascimento_fim' => 2010, 'genero' => 'misto', 'ativo' => true, 'ordem' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Juniores', 'codigo' => 'JUN', 'idade_minima' => 18, 'idade_maxima' => 19, 'ano_nascimento_inicio' => 2007, 'ano_nascimento_fim' => 2008, 'genero' => 'misto', 'ativo' => true, 'ordem' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Seniores', 'codigo' => 'SEN', 'idade_minima' => 20, 'idade_maxima' => 99, 'ano_nascimento_inicio' => 1925, 'ano_nascimento_fim' => 2006, 'genero' => 'misto', 'ativo' => true, 'ordem' => 8, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('escaloes')->insert($escaloes);
    }
}
