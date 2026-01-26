<?php

namespace App\Services;

use App\Models\User;
use App\Models\RelacaoUser;
use App\Models\DadosPessoais;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RelacaoService
{
    /**
     * Sincroniza relação encarregado de educação ↔ educando
     * Cria relações bidirecionais automaticamente
     * 
     * @param int $menorId ID do utilizador menor
     * @param array $encarregadosIds Array de IDs dos encarregados de educação
     * @param int $clubId ID do clube
     * @return void
     * @throws \Exception
     */
    public function syncEncarregadosEducacao(int $menorId, array $encarregadosIds, int $clubId): void
    {
        DB::beginTransaction();
        
        try {
            // Validar que o menor tem o campo menor = true
            $dadosPessoaisMenor = DadosPessoais::where('user_id', $menorId)->first();
            
            if (!$dadosPessoaisMenor || !$dadosPessoaisMenor->menor) {
                throw new \Exception("Utilizador {$menorId} não está marcado como menor");
            }

            // Validar que todos os encarregados existem e têm o tipo correto
            foreach ($encarregadosIds as $encarregadoId) {
                $user = User::find($encarregadoId);
                if (!$user) {
                    throw new \Exception("Encarregado de educação {$encarregadoId} não encontrado");
                }
                
                // Verificar se tem tipo "Encarregado de Educação"
                $hasEEType = DB::table('user_tipos_utilizador')
                    ->join('tipos_utilizador', 'user_tipos_utilizador.tipo_utilizador_id', '=', 'tipos_utilizador.id')
                    ->where('user_tipos_utilizador.user_id', $encarregadoId)
                    ->where('user_tipos_utilizador.club_id', $clubId)
                    ->where('tipos_utilizador.slug', 'encarregado_educacao')
                    ->exists();
                    
                if (!$hasEEType) {
                    throw new \Exception("Utilizador {$encarregadoId} não tem o tipo 'Encarregado de Educação'");
                }
            }

            // Remover relações antigas (desativar)
            RelacaoUser::where('club_id', $clubId)
                ->where('user_destino_id', $menorId)
                ->where('tipo_relacao', 'encarregado_educacao')
                ->update(['ativo' => false, 'data_fim' => now()]);
                
            RelacaoUser::where('club_id', $clubId)
                ->where('user_origem_id', $menorId)
                ->where('tipo_relacao', 'educando')
                ->update(['ativo' => false, 'data_fim' => now()]);

            // Criar novas relações bidirecionais
            foreach ($encarregadosIds as $encarregadoId) {
                // Relação: EE → Menor (tipo: encarregado_educacao)
                RelacaoUser::create([
                    'club_id' => $clubId,
                    'user_origem_id' => $encarregadoId,
                    'user_destino_id' => $menorId,
                    'tipo_relacao' => 'encarregado_educacao',
                    'data_inicio' => now(),
                    'ativo' => true,
                ]);

                // Relação inversa: Menor → EE (tipo: educando)
                RelacaoUser::create([
                    'club_id' => $clubId,
                    'user_origem_id' => $menorId,
                    'user_destino_id' => $encarregadoId,
                    'tipo_relacao' => 'educando',
                    'data_inicio' => now(),
                    'ativo' => true,
                ]);
                
                Log::info("Relação criada: EE {$encarregadoId} ↔ Menor {$menorId} no clube {$clubId}");
            }

            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao sincronizar encarregados de educação: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Sincroniza educandos para um encarregado de educação
     * 
     * @param int $encarregadoId ID do encarregado de educação
     * @param array $educandosIds Array de IDs dos educandos (menores)
     * @param int $clubId ID do clube
     * @return void
     * @throws \Exception
     */
    public function syncEducandos(int $encarregadoId, array $educandosIds, int $clubId): void
    {
        DB::beginTransaction();
        
        try {
            // Validar que o encarregado tem o tipo correto
            $hasEEType = DB::table('user_tipos_utilizador')
                ->join('tipos_utilizador', 'user_tipos_utilizador.tipo_utilizador_id', '=', 'tipos_utilizador.id')
                ->where('user_tipos_utilizador.user_id', $encarregadoId)
                ->where('user_tipos_utilizador.club_id', $clubId)
                ->where('tipos_utilizador.slug', 'encarregado_educacao')
                ->exists();
                
            if (!$hasEEType) {
                throw new \Exception("Utilizador {$encarregadoId} não tem o tipo 'Encarregado de Educação'");
            }

            // Validar que todos os educandos são menores
            foreach ($educandosIds as $educandoId) {
                $dadosPessoais = DadosPessoais::where('user_id', $educandoId)->first();
                
                if (!$dadosPessoais || !$dadosPessoais->menor) {
                    throw new \Exception("Utilizador {$educandoId} não está marcado como menor");
                }
            }

            // Remover relações antigas
            RelacaoUser::where('club_id', $clubId)
                ->where('user_origem_id', $encarregadoId)
                ->where('tipo_relacao', 'encarregado_educacao')
                ->update(['ativo' => false, 'data_fim' => now()]);
                
            RelacaoUser::where('club_id', $clubId)
                ->where('user_destino_id', $encarregadoId)
                ->where('tipo_relacao', 'educando')
                ->update(['ativo' => false, 'data_fim' => now()]);

            // Criar novas relações bidirecionais
            foreach ($educandosIds as $educandoId) {
                // Relação: EE → Educando
                RelacaoUser::create([
                    'club_id' => $clubId,
                    'user_origem_id' => $encarregadoId,
                    'user_destino_id' => $educandoId,
                    'tipo_relacao' => 'encarregado_educacao',
                    'data_inicio' => now(),
                    'ativo' => true,
                ]);

                // Relação inversa: Educando → EE
                RelacaoUser::create([
                    'club_id' => $clubId,
                    'user_origem_id' => $educandoId,
                    'user_destino_id' => $encarregadoId,
                    'tipo_relacao' => 'educando',
                    'data_inicio' => now(),
                    'ativo' => true,
                ]);
                
                Log::info("Relação criada: EE {$encarregadoId} ↔ Educando {$educandoId} no clube {$clubId}");
            }

            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao sincronizar educandos: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Obter encarregados de educação de um menor
     * 
     * @param int $menorId
     * @param int $clubId
     * @return \Illuminate\Support\Collection
     */
    public function getEncarregadosEducacao(int $menorId, int $clubId)
    {
        return User::whereHas('relacoes', function ($query) use ($menorId, $clubId) {
            $query->where('club_id', $clubId)
                ->where('user_destino_id', $menorId)
                ->where('tipo_relacao', 'encarregado_educacao')
                ->where('ativo', true);
        })->get();
    }

    /**
     * Obter educandos de um encarregado de educação
     * 
     * @param int $encarregadoId
     * @param int $clubId
     * @return \Illuminate\Support\Collection
     */
    public function getEducandos(int $encarregadoId, int $clubId)
    {
        return User::whereHas('relacoes', function ($query) use ($encarregadoId, $clubId) {
            $query->where('club_id', $clubId)
                ->where('user_origem_id', $encarregadoId)
                ->where('tipo_relacao', 'encarregado_educacao')
                ->where('ativo', true);
        })->get();
    }
}
