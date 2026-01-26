<?php

namespace App\Services\Membros;

use App\Models\User;
use App\Models\Membro;
use App\Models\DadosPessoais;
use App\Models\DadosConfiguracao;
use App\Models\ClubUser;
use App\Models\UserTipoUtilizador;
use App\Models\Atleta;
use App\Models\DadosDesportivos;
use App\Models\AtletaEscalao;
use App\Models\DadosFinanceiros;
use App\Services\Tenancy\ClubContext;
use Illuminate\Support\Facades\DB;

/**
 * Service para criação e gestão completa de membros
 * Conforme especificação: cria User + Membro + dados relacionados
 */
class MembroService
{
    protected ClubContext $clubContext;

    public function __construct(ClubContext $clubContext)
    {
        $this->clubContext = $clubContext;
    }

    /**
     * Criar um membro completo com todos os dados
     * 
     * @param array $dados Dados completos do membro
     * @return Membro
     */
    public function criarMembro(array $dados): Membro
    {
        return DB::transaction(function () use ($dados) {
            $clubId = $this->clubContext->getActiveClubId();
            
            if (!$clubId) {
                throw new \Exception('Clube não definido no contexto');
            }

            // 1. Garantir User
            $user = $this->garantirUser($dados['user'] ?? []);

            // 2. Garantir club_users (associar user ao clube)
            $this->garantirClubUser($clubId, $user->id, $dados['club_user'] ?? []);

            // 3. Upsert dados_pessoais
            $this->upsertDadosPessoais($user->id, $dados['dados_pessoais'] ?? []);

            // 4. Create membros
            $membro = $this->criarMembroRecord($clubId, $user->id, $dados['membro'] ?? []);

            // 5. Upsert dados_configuracao
            $this->upsertDadosConfiguracao($clubId, $user->id, $dados['dados_configuracao'] ?? []);

            // 6. Attach tipos_utilizador
            if (!empty($dados['tipos_utilizador'])) {
                $this->attachTiposUtilizador($clubId, $user->id, $dados['tipos_utilizador']);
            }

            // 7. Se tipo=atleta, criar dados desportivos
            if ($this->isAtleta($dados['tipos_utilizador'] ?? [])) {
                $this->criarDadosAtleta($clubId, $membro->id, $dados['dados_desportivos'] ?? []);
            }

            // 8. Se tem mensalidade, criar dados_financeiros
            if (!empty($dados['dados_financeiros'])) {
                $this->criarDadosFinanceiros($clubId, $membro->id, $dados['dados_financeiros']);
            }

            return $membro->fresh();
        });
    }

    /**
     * Garantir que o User existe (criar ou atualizar)
     */
    protected function garantirUser(array $dados): User
    {
        // Se tem ID, buscar
        if (!empty($dados['id'])) {
            $user = User::find($dados['id']);
            if ($user) {
                return $user;
            }
        }

        // Se tem email, buscar por email
        if (!empty($dados['email'])) {
            $user = User::where('email', $dados['email'])->first();
            if ($user) {
                // Email já existe, retornar o user existente
                return $user;
            }
        }

        // Criar novo user
        $userData = [
            'name' => $dados['name'] ?? $dados['nome'] ?? 'Sem nome',
            'email' => $dados['email'] ?? null,
            'password' => isset($dados['password']) ? bcrypt($dados['password']) : bcrypt('password123'),
            'telefone' => $dados['telefone'] ?? null,
        ];
        
        // Usar DB::statement para inserir com cast explícito de boolean
        $userId = DB::selectOne(
            "INSERT INTO users (name, email, password, telefone, ativo, created_at, updated_at) 
             VALUES (?, ?, ?, ?, ?::boolean, NOW(), NOW()) RETURNING id",
            [
                $userData['name'],
                $userData['email'],
                $userData['password'],
                $userData['telefone'],
                $dados['ativo'] ?? true
            ]
        )->id;
        
        return User::find($userId);
    }

    /**
     * Garantir associação user ↔ clube
     */
    protected function garantirClubUser(int $clubId, int $userId, array $dados): ClubUser
    {
        $clubUser = ClubUser::where('club_id', $clubId)
            ->where('user_id', $userId)
            ->whereRaw('ativo = true')
            ->first();

        if ($clubUser) {
            return $clubUser;
        }

        $clubUserId = DB::selectOne(
            "INSERT INTO club_users (club_id, user_id, role_no_clube, ativo, data_inicio, created_at, updated_at) 
             VALUES (?, ?, ?, ?::boolean, ?, NOW(), NOW()) RETURNING id",
            [
                $clubId,
                $userId,
                $dados['role_no_clube'] ?? null,
                true,
                $dados['data_inicio'] ?? now()
            ]
        )->id;
        
        return ClubUser::find($clubUserId);
    }

    /**
     * Criar/atualizar dados pessoais
     */
    protected function upsertDadosPessoais(int $userId, array $dados): void
    {
        DadosPessoais::updateOrCreate(
            ['user_id' => $userId],
            [
                'nome_completo' => $dados['nome_completo'] ?? '',
                'data_nascimento' => $dados['data_nascimento'] ?? null,
                'nif' => $dados['nif'] ?? null,
                'cc' => $dados['cc'] ?? null,
                'morada' => $dados['morada'] ?? null,
                'codigo_postal' => $dados['codigo_postal'] ?? null,
                'localidade' => $dados['localidade'] ?? null,
                'nacionalidade' => $dados['nacionalidade'] ?? null,
                'sexo' => $dados['sexo'] ?? null,
                'contacto_telefonico' => $dados['contacto_telefonico'] ?? null,
                'email_secundario' => $dados['email_secundario'] ?? null,
            ]
        );
    }

    /**
     * Criar registo de membro
     */
    protected function criarMembroRecord(int $clubId, int $userId, array $dados): Membro
    {
        $estado = $dados['estado'] ?? 'ativo';
        $numeroSocio = $dados['numero_socio'] ?? null;
        $dataAdesao = $dados['data_adesao'] ?? now();
        $observacoes = $dados['observacoes'] ?? null;
        
        $id = DB::selectOne(
            "INSERT INTO membros (club_id, user_id, numero_socio, estado, data_adesao, observacoes, created_at, updated_at) 
             VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW()) 
             RETURNING id",
            [$clubId, $userId, $numeroSocio, $estado, $dataAdesao, $observacoes]
        )->id;
        
        return Membro::find($id);
    }

    /**
     * Criar/atualizar dados de configuração
     */
    protected function upsertDadosConfiguracao(int $clubId, int $userId, array $dados): void
    {
        $exists = DadosConfiguracao::where('club_id', $clubId)
            ->where('user_id', $userId)
            ->exists();
            
        if ($exists) {
            DB::statement(
                "UPDATE dados_configuracao 
                 SET rgpd = ?::boolean, data_rgpd = ?, consentimento = ?::boolean, 
                     data_consentimento = ?, afiliacao = ?::boolean, data_afiliacao = ?, 
                     declaracao_transporte = ?::boolean, email_utilizador = ?, updated_at = NOW()
                 WHERE club_id = ? AND user_id = ?",
                [
                    $dados['rgpd'] ?? false,
                    $dados['data_rgpd'] ?? null,
                    $dados['consentimento'] ?? false,
                    $dados['data_consentimento'] ?? null,
                    $dados['afiliacao'] ?? false,
                    $dados['data_afiliacao'] ?? null,
                    $dados['declaracao_transporte'] ?? false,
                    $dados['email_utilizador'] ?? null,
                    $clubId,
                    $userId
                ]
            );
        } else {
            DB::statement(
                "INSERT INTO dados_configuracao 
                 (club_id, user_id, rgpd, data_rgpd, consentimento, data_consentimento, 
                  afiliacao, data_afiliacao, declaracao_transporte, email_utilizador, created_at, updated_at)
                 VALUES (?, ?, ?::boolean, ?, ?::boolean, ?, ?::boolean, ?, ?::boolean, ?, NOW(), NOW())",
                [
                    $clubId,
                    $userId,
                    $dados['rgpd'] ?? false,
                    $dados['data_rgpd'] ?? null,
                    $dados['consentimento'] ?? false,
                    $dados['data_consentimento'] ?? null,
                    $dados['afiliacao'] ?? false,
                    $dados['data_afiliacao'] ?? null,
                    $dados['declaracao_transporte'] ?? false,
                    $dados['email_utilizador'] ?? null
                ]
            );
        }
    }

    /**
     * Associar tipos de utilizador
     */
    protected function attachTiposUtilizador(int $clubId, int $userId, array $tipos): void
    {
        foreach ($tipos as $tipoId) {
            $dataInicio = now();
            
            $exists = UserTipoUtilizador::where('club_id', $clubId)
                ->where('user_id', $userId)
                ->where('tipo_utilizador_id', $tipoId)
                ->where('data_inicio', $dataInicio)
                ->exists();
            
            if (!$exists) {
                DB::statement(
                    "INSERT INTO user_tipos_utilizador 
                     (club_id, user_id, tipo_utilizador_id, data_inicio, ativo, created_at, updated_at)
                     VALUES (?, ?, ?, ?, ?::boolean, NOW(), NOW())",
                    [$clubId, $userId, $tipoId, $dataInicio, true]
                );
            }
        }
    }

    /**
     * Verificar se é atleta
     */
    protected function isAtleta(array $tipos): bool
    {
        // Assumindo que tipo_id = 1 é "Atleta"
        // Ajustar conforme necessário
        return in_array(1, $tipos) || in_array('atleta', array_map('strtolower', $tipos));
    }

    /**
     * Criar dados desportivos (atleta)
     */
    protected function criarDadosAtleta(int $clubId, int $membroId, array $dados): void
    {
        // Criar atleta
        $atletaId = DB::selectOne(
            "INSERT INTO atletas (club_id, membro_id, ativo, created_at, updated_at)
             VALUES (?, ?, ?::boolean, NOW(), NOW())
             RETURNING id",
            [$clubId, $membroId, true]
        )->id;
        
        $atleta = Atleta::find($atletaId);

        // Criar dados desportivos
        DadosDesportivos::create([
            'club_id' => $clubId,
            'atleta_id' => $atleta->id,
            'num_federacao' => $dados['num_federacao'] ?? null,
            'numero_pmb' => $dados['numero_pmb'] ?? null,
            'data_inscricao' => $dados['data_inscricao'] ?? now(),
            'escalao_atual_id' => $dados['escalao_atual_id'] ?? null,
            'data_atestado_medico' => $dados['data_atestado_medico'] ?? null,
            'informacoes_medicas' => $dados['informacoes_medicas'] ?? null,
        ]);

        // Se tem escalão, criar histórico
        if (!empty($dados['escalao_atual_id'])) {
            AtletaEscalao::create([
                'club_id' => $clubId,
                'atleta_id' => $atleta->id,
                'escalao_id' => $dados['escalao_atual_id'],
                'data_inicio' => now(),
            ]);
        }
    }

    /**
     * Criar dados financeiros
     */
    protected function criarDadosFinanceiros(int $clubId, int $membroId, array $dados): void
    {
        DadosFinanceiros::create([
            'club_id' => $clubId,
            'membro_id' => $membroId,
            'mensalidade_id' => $dados['mensalidade_id'] ?? null,
            'dia_cobranca' => $dados['dia_cobranca'] ?? null,
            'observacoes' => $dados['observacoes'] ?? null,
        ]);
    }

    /**
     * Atualizar membro
     */
    public function atualizarMembro(int $membroId, array $dados): Membro
    {
        return DB::transaction(function () use ($membroId, $dados) {
            $membro = Membro::findOrFail($membroId);

            // Atualizar dados básicos do membro
            if (isset($dados['membro'])) {
                $membro->update($dados['membro']);
            }

            // Atualizar dados pessoais
            if (isset($dados['dados_pessoais'])) {
                $this->upsertDadosPessoais($membro->user_id, $dados['dados_pessoais']);
            }

            // Atualizar configuração
            if (isset($dados['dados_configuracao'])) {
                $this->upsertDadosConfiguracao($membro->club_id, $membro->user_id, $dados['dados_configuracao']);
            }

            return $membro->fresh();
        });
    }
}
