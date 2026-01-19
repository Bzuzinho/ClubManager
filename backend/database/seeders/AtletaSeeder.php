<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Membro;
use App\Models\Atleta;
use App\Models\MembroTipo;
use App\Models\TipoMembro;

class AtletaSeeder extends Seeder
{
    public function run(): void
    {
        $membro = Membro::first();
        $tipoAtleta = TipoMembro::where('slug', 'atleta')->first();

        if (!$membro || !$tipoAtleta) {
            return;
        }

        // Histórico de tipo atleta
        MembroTipo::create([
            'membro_id' => $membro->id,
            'tipo_membro_id' => $tipoAtleta->id,
            'data_inicio' => now()->subYears(4),
        ]);

        // Atleta ativo
        Atleta::create([
            'membro_id' => $membro->id,
            'ativo' => true,
            'data_inicio' => now()->subYears(4),
        ]);
    }
}
