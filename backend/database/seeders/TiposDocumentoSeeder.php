<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            ['nome' => 'Cartão de Cidadão / BI', 'codigo' => 'CC', 'descricao' => 'Documento de identificação', 'obrigatorio' => true, 'tem_validade' => true, 'validade_meses' => null, 'aplicavel_a' => 'todos', 'ativo' => true, 'ordem' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Atestado Médico', 'codigo' => 'ATESTADO', 'descricao' => 'Atestado médico desportivo', 'obrigatorio' => true, 'tem_validade' => true, 'validade_meses' => 12, 'aplicavel_a' => 'atleta', 'ativo' => true, 'ordem' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Autorização RGPD', 'codigo' => 'RGPD', 'descricao' => 'Consentimento tratamento de dados', 'obrigatorio' => true, 'tem_validade' => false, 'validade_meses' => null, 'aplicavel_a' => 'todos', 'ativo' => true, 'ordem' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Seguro Desportivo', 'codigo' => 'SEGURO', 'descricao' => 'Apólice de seguro desportivo', 'obrigatorio' => true, 'tem_validade' => true, 'validade_meses' => 12, 'aplicavel_a' => 'atleta', 'ativo' => true, 'ordem' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Fotografia', 'codigo' => 'FOTO', 'descricao' => 'Fotografia tipo passe', 'obrigatorio' => false, 'tem_validade' => false, 'validade_meses' => null, 'aplicavel_a' => 'todos', 'ativo' => true, 'ordem' => 5, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('tipos_documento')->insert($tipos);
    }
}
