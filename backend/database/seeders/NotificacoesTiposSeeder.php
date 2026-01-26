<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotificacaoTipo;

class NotificacoesTiposSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'slug' => 'genericas',
                'nome' => 'Notificações Genéricas',
                'descricao' => 'Notificações gerais do sistema',
            ],
            [
                'slug' => 'pagamentos_novos',
                'nome' => 'Novos Pagamentos',
                'descricao' => 'Notificações de novos pagamentos recebidos',
            ],
            [
                'slug' => 'atividades',
                'nome' => 'Atividades',
                'descricao' => 'Notificações relacionadas com treinos e eventos',
            ],
            [
                'slug' => 'faturas',
                'nome' => 'Faturas',
                'descricao' => 'Notificações de emissão de faturas',
            ],
        ];

        foreach ($tipos as $tipo) {
            NotificacaoTipo::create($tipo);
        }
    }
}
