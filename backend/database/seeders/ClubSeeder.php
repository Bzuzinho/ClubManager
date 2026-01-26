<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Club;
use Illuminate\Support\Facades\DB;

class ClubSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar se já existe para não duplicar
        if (Club::where('abreviatura', 'BSCN')->exists()) {
            $this->command->info('Club BSCN já existe, pulando...');
            return;
        }

        // Usar DB::statement para inserir com cast explícito no PostgreSQL
        DB::statement("
            INSERT INTO clubs (nome_fiscal, abreviatura, nif, morada, contacto_telefonico, email, ativo, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?::boolean, NOW(), NOW())
        ", [
            'Bairro dos Sesimbra Clube de Natação',
            'BSCN',
            '123456789',
            'Rua Exemplo, 123',
            '+351 212 123 456',
            'geral@bscn.pt',
            true
        ]);
        
        $this->command->info('✅ Club BSCN criado com sucesso!');
    }
}
