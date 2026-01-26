<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodosPagamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $metodos = [
            ['nome' => 'Dinheiro', 'codigo' => 'DINHEIRO', 'descricao' => 'Pagamento em dinheiro', 'requer_comprovativo' => false, 'ativo' => true, 'ordem' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'MB Way', 'codigo' => 'MBWAY', 'descricao' => 'Pagamento via MB Way', 'requer_comprovativo' => true, 'ativo' => true, 'ordem' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Multibanco', 'codigo' => 'MB', 'descricao' => 'Pagamento por referência Multibanco', 'requer_comprovativo' => true, 'ativo' => true, 'ordem' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Transferência Bancária', 'codigo' => 'TRANSF', 'descricao' => 'Transferência bancária', 'requer_comprovativo' => true, 'ativo' => true, 'ordem' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Débito Direto', 'codigo' => 'DD', 'descricao' => 'Débito direto SEPA', 'requer_comprovativo' => false, 'ativo' => true, 'ordem' => 5, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('metodos_pagamento')->insert($metodos);
    }
}
