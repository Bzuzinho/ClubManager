<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Club;
use App\Models\Escalao;
use App\Models\TipoUtilizador;
use App\Models\Prova;
use App\Models\CentroCusto;
use App\Models\NotificacaoConfig;
use App\Models\NotificacaoTipo;

class ConfiguracaoClubSeeder extends Seeder
{
    public function run(): void
    {
        $club = Club::first();

        if (!$club) {
            $this->command->error('Nenhum clube encontrado. Execute ClubSeeder primeiro.');
            return;
        }

        // Escalões
        $escaloes = [
            ['nome' => 'Infantis A', 'idade_minima' => 8, 'idade_maxima' => 9],
            ['nome' => 'Infantis B', 'idade_minima' => 10, 'idade_maxima' => 11],
            ['nome' => 'Iniciados', 'idade_minima' => 12, 'idade_maxima' => 13],
            ['nome' => 'Juvenis', 'idade_minima' => 14, 'idade_maxima' => 15],
            ['nome' => 'Juniores', 'idade_minima' => 16, 'idade_maxima' => 17],
            ['nome' => 'Seniores', 'idade_minima' => 18, 'idade_maxima' => null],
        ];

        foreach ($escaloes as $escalao) {
            Escalao::create(array_merge(['club_id' => $club->id], $escalao));
        }

        // Tipos de Utilizador
        $tipos = [
            ['nome' => 'Atleta', 'descricao' => 'Praticante de natação'],
            ['nome' => 'Encarregado de Educação', 'descricao' => 'Responsável legal'],
            ['nome' => 'Treinador', 'descricao' => 'Técnico de natação'],
            ['nome' => 'Funcionário', 'descricao' => 'Colaborador do clube'],
            ['nome' => 'Dirigente', 'descricao' => 'Membro da direção'],
        ];

        foreach ($tipos as $tipo) {
            TipoUtilizador::create(array_merge(['club_id' => $club->id], $tipo));
        }

        // Provas
        $provas = [
            ['nome' => '50m Livres', 'distancia_m' => 50, 'modalidade' => 'Livres', 'individual' => true],
            ['nome' => '100m Livres', 'distancia_m' => 100, 'modalidade' => 'Livres', 'individual' => true],
            ['nome' => '200m Livres', 'distancia_m' => 200, 'modalidade' => 'Livres', 'individual' => true],
            ['nome' => '50m Costas', 'distancia_m' => 50, 'modalidade' => 'Costas', 'individual' => true],
            ['nome' => '100m Costas', 'distancia_m' => 100, 'modalidade' => 'Costas', 'individual' => true],
            ['nome' => '50m Bruços', 'distancia_m' => 50, 'modalidade' => 'Bruços', 'individual' => true],
            ['nome' => '100m Bruços', 'distancia_m' => 100, 'modalidade' => 'Bruços', 'individual' => true],
            ['nome' => '50m Mariposa', 'distancia_m' => 50, 'modalidade' => 'Mariposa', 'individual' => true],
            ['nome' => '100m Mariposa', 'distancia_m' => 100, 'modalidade' => 'Mariposa', 'individual' => true],
            ['nome' => '200m Estilos', 'distancia_m' => 200, 'modalidade' => 'Estilos', 'individual' => true],
            ['nome' => '4x50m Livres', 'distancia_m' => 200, 'modalidade' => 'Livres', 'individual' => false],
        ];

        foreach ($provas as $prova) {
            Prova::create(array_merge(['club_id' => $club->id], $prova));
        }

        // Centros de Custo
        $centros = [
            ['nome' => 'Natação Formação', 'tipo' => 'desportivo', 'descricao' => 'Custos com escalões de formação'],
            ['nome' => 'Natação Competição', 'tipo' => 'desportivo', 'descricao' => 'Custos com natação competitiva'],
            ['nome' => 'Administração', 'tipo' => 'administrativo', 'descricao' => 'Custos administrativos gerais'],
            ['nome' => 'Marketing', 'tipo' => 'marketing', 'descricao' => 'Custos com comunicação e marketing'],
            ['nome' => 'Instalações', 'tipo' => 'operacional', 'descricao' => 'Manutenção de instalações'],
        ];

        foreach ($centros as $centro) {
            CentroCusto::create(array_merge(['club_id' => $club->id], $centro));
        }

        // Configuração de notificações
        $tiposNotificacao = NotificacaoTipo::all();
        foreach ($tiposNotificacao as $tipo) {
            NotificacaoConfig::create([
                'club_id' => $club->id,
                'tipo_id' => $tipo->id,
                'ativo' => true,
            ]);
        }
    }
}
