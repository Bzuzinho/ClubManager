<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pessoa;
use App\Models\Membro;

class PessoasMembrosSeeder extends Seeder
{
    public function run(): void
    {
        $pessoa = Pessoa::create([
            'nome_completo' => 'Ricardo Ferreira',
            'data_nascimento' => '1982-05-12',
            'sexo' => 'masculino',
            'nacionalidade' => 'Portuguesa',
            'contacto_telefonico' => '912345678',
            'email_secundario' => 'ricardo.teste@email.pt',
            'localidade' => 'Benedita',
            'menor' => false,
        ]);

        Membro::create([
            'pessoa_id' => $pessoa->id,
            'numero_socio' => 'SOC-0001',
            'estado' => 'ativo',
            'data_entrada' => now()->subYears(5),
        ]);
    }
}
