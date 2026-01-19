<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Escalao;

class EscaloesSeeder extends Seeder
{
    public function run(): void
    {
        $escaloes = [
            ['nome' => 'Infantil', 'idade_min' => 6, 'idade_max' => 9],
            ['nome' => 'Juvenil', 'idade_min' => 10, 'idade_max' => 13],
            ['nome' => 'Júnior', 'idade_min' => 14, 'idade_max' => 17],
            ['nome' => 'Sénior', 'idade_min' => 18, 'idade_max' => 39],
            ['nome' => 'Master', 'idade_min' => 40, 'idade_max' => 99],
        ];

        foreach ($escaloes as $escalao) {
            Escalao::firstOrCreate(
                ['nome' => $escalao['nome']],
                $escalao
            );
        }
    }
}
