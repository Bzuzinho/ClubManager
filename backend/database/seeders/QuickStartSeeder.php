<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class QuickStartSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar se já existe
        if (DB::table('clubs')->where('abreviatura', 'BSCN')->exists()) {
            $this->command->info("ℹ️  Dados já existem. Pulando criação.");
            $clubId = DB::table('clubs')->where('abreviatura', 'BSCN')->value('id');
            $userId = DB::table('users')->where('email', 'admin@admin.pt')->value('id');
            $this->command->info("✅ Club ID: $clubId");
            $this->command->info("✅ User ID: $userId");
            $this->command->info("📧 Login: admin@admin.pt");
            $this->command->info("🔑 Password: password");
            return;
        }

        // Criar clube usando SQL direto para contornar bug PostgreSQL
        DB::statement("
            INSERT INTO clubs (nome_fiscal, abreviatura, nif, morada, contacto_telefonico, email, ativo, created_at, updated_at) 
            VALUES ('Bairro dos Sesimbra Clube de Natação', 'BSCN', '123456789', 'Rua Exemplo, 123', '+351 212 123 456', 'geral@bscn.pt', true, NOW(), NOW())
        ");
        $clubId = DB::table('clubs')->where('abreviatura', 'BSCN')->value('id');
        $this->command->info("✅ Club criado (ID: $clubId)");

        // Criar user admin
        $passwordHash = Hash::make('password');
        DB::statement("
            INSERT INTO users (name, email, password, created_at, updated_at) 
            VALUES ('Admin', 'admin@admin.pt', '$passwordHash', NOW(), NOW())
        ");
        $userId = DB::table('users')->where('email', 'admin@admin.pt')->value('id');
        $this->command->info("✅ User admin criado (ID: $userId)");

        // Associar user ao clube
        DB::table('club_users')->insert([
            'club_id' => $clubId,
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->command->info("✅ User associado ao clube");

        // Criar roles Spatie (apenas se não existirem)
        $roles = ['super-admin', 'admin', 'secretaria', 'treinador', 'encarregado'];
        foreach ($roles as $roleName) {
            if (!DB::table('roles')->where('name', $roleName)->exists()) {
                DB::table('roles')->insert([
                    'name' => $roleName,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        $this->command->info("✅ Roles criadas: " . implode(', ', $roles));

        // Atribuir role super-admin ao user
        $roleId = DB::table('roles')->where('name', 'super-admin')->value('id');
        if (!DB::table('model_has_roles')->where('model_id', $userId)->where('role_id', $roleId)->exists()) {
            DB::table('model_has_roles')->insert([
                'role_id' => $roleId,
                'model_type' => 'App\\Models\\User',
                'model_id' => $userId,
            ]);
        }
        $this->command->info("✅ Role super-admin atribuída ao admin");

        // Criar alguns dados de exemplo
        $this->createSampleData($clubId);

        $this->command->info("\n🎉 Dados base criados com sucesso!");
        $this->command->info("📧 Login: admin@admin.pt");
        $this->command->info("🔑 Password: password");
    }

    private function createSampleData(int $clubId): void
    {
        // Criar escalões
        $escaloes = [
            ['club_id' => $clubId, 'nome' => 'Infantis', 'ativo' => true],
            ['club_id' => $clubId, 'nome' => 'Juvenis', 'ativo' => true],
            ['club_id' => $clubId, 'nome' => 'Juniores', 'ativo' => true],
            ['club_id' => $clubId, 'nome' => 'Seniores', 'ativo' => true],
        ];
        
        foreach ($escaloes as $escalao) {
            DB::table('escaloes')->insert(array_merge($escalao, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
        $this->command->info("✅ 4 Escalões criados");

        // Criar tipos de utilizador
        $tipos = [
            ['club_id' => $clubId, 'nome' => 'Atleta', 'descricao' => 'Praticante de natação'],
            ['club_id' => $clubId, 'nome' => 'Encarregado de Educação', 'descricao' => 'Responsável por atleta'],
            ['club_id' => $clubId, 'nome' => 'Treinador', 'descricao' => 'Treinador de natação'],
            ['club_id' => $clubId, 'nome' => 'Dirigente', 'descricao' => 'Membro da direção'],
        ];
        
        foreach ($tipos as $tipo) {
            DB::table('tipos_utilizador')->insert(array_merge($tipo, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
        $this->command->info("✅ 4 Tipos de utilizador criados");
    }
}
