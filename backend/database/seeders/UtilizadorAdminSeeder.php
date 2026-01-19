<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Utilizador;
use App\Models\Pessoa;
use Illuminate\Support\Facades\Hash;

class UtilizadorAdminSeeder extends Seeder
{
    public function run(): void
    {
        $pessoa = Pessoa::first();

        if (!$pessoa) {
            return;
        }

        Utilizador::firstOrCreate(
            ['pessoa_id' => $pessoa->id],
            [
                'email_utilizador' => 'admin@clubmanager.pt',
                'password' => Hash::make('password'),
                'ativo_login' => true,
            ]
        );
    }
}
