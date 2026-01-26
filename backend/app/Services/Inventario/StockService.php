<?php

namespace App\Services\Inventario;

use App\Models\Material;
use App\Models\MovimentoStock;
use App\Models\Emprestimo;
use App\Services\Tenancy\ClubContext;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Service para gestão de inventário e stock
 * Conforme especificação
 */
class StockService
{
    protected ClubContext $clubContext;

    public function __construct(ClubContext $clubContext)
    {
        $this->clubContext = $clubContext;
    }

    /**
     * Registar movimento de entrada
     */
    public function registarEntrada(int $materialId, int $quantidade, array $dados = []): MovimentoStock
    {
        return DB::transaction(function () use ($materialId, $quantidade, $dados) {
            $clubId = $this->clubContext->getActiveClubId();
            
            $material = Material::where('id', $materialId)
                ->where('club_id', $clubId)
                ->firstOrFail();

            // Criar movimento
            $movimento = MovimentoStock::create([
                'club_id' => $clubId,
                'material_id' => $materialId,
                'tipo' => 'entrada',
                'quantidade' => $quantidade,
                'data_movimento' => $dados['data_movimento'] ?? now(),
                'preco_unitario' => $dados['preco_unitario'] ?? null,
                'fornecedor_id' => $dados['fornecedor_id'] ?? null,
                'centro_custo_id' => $dados['centro_custo_id'] ?? null,
                'observacoes' => $dados['observacoes'] ?? null,
            ]);

            // Atualizar stock do material
            $material->increment('quantidade_atual', $quantidade);

            return $movimento;
        });
    }

    /**
     * Registar movimento de saída
     */
    public function registarSaida(int $materialId, int $quantidade, array $dados = []): MovimentoStock
    {
        return DB::transaction(function () use ($materialId, $quantidade, $dados) {
            $clubId = $this->clubContext->getActiveClubId();
            
            $material = Material::where('id', $materialId)
                ->where('club_id', $clubId)
                ->firstOrFail();

            // Validar stock disponível
            if ($material->quantidade_atual < $quantidade) {
                throw new \Exception('Stock insuficiente. Disponível: ' . $material->quantidade_atual);
            }

            // Criar movimento
            $movimento = MovimentoStock::create([
                'club_id' => $clubId,
                'material_id' => $materialId,
                'tipo' => 'saida',
                'quantidade' => $quantidade,
                'data_movimento' => $dados['data_movimento'] ?? now(),
                'centro_custo_id' => $dados['centro_custo_id'] ?? null,
                'observacoes' => $dados['observacoes'] ?? null,
            ]);

            // Atualizar stock do material
            $material->decrement('quantidade_atual', $quantidade);

            return $movimento;
        });
    }

    /**
     * Registar ajuste de stock (inventário físico)
     */
    public function registarAjuste(int $materialId, int $quantidadeNova, string $observacoes = null): MovimentoStock
    {
        return DB::transaction(function () use ($materialId, $quantidadeNova, $observacoes) {
            $clubId = $this->clubContext->getActiveClubId();
            
            $material = Material::where('id', $materialId)
                ->where('club_id', $clubId)
                ->firstOrFail();

            $diferencaQuantidade = $quantidadeNova - $material->quantidade_atual;
            $tipoMovimento = $diferencaQuantidade > 0 ? 'ajuste_positivo' : 'ajuste_negativo';

            // Criar movimento
            $movimento = MovimentoStock::create([
                'club_id' => $clubId,
                'material_id' => $materialId,
                'tipo' => $tipoMovimento,
                'quantidade' => abs($diferencaQuantidade),
                'data_movimento' => now(),
                'observacoes' => $observacoes ?? 'Ajuste de inventário',
            ]);

            // Atualizar stock do material
            $material->update(['quantidade_atual' => $quantidadeNova]);

            return $movimento;
        });
    }

    /**
     * Criar empréstimo
     */
    public function criarEmprestimo(int $materialId, int $quantidade, array $dados): Emprestimo
    {
        return DB::transaction(function () use ($materialId, $quantidade, $dados) {
            $clubId = $this->clubContext->getActiveClubId();
            
            $material = Material::where('id', $materialId)
                ->where('club_id', $clubId)
                ->firstOrFail();

            // Validar stock disponível
            if ($material->quantidade_atual < $quantidade) {
                throw new \Exception('Stock insuficiente para empréstimo. Disponível: ' . $material->quantidade_atual);
            }

            // Criar empréstimo
            $emprestimo = Emprestimo::create([
                'club_id' => $clubId,
                'material_id' => $materialId,
                'membro_id' => $dados['membro_id'] ?? null,
                'nome_pessoa' => $dados['nome_pessoa'] ?? null,
                'quantidade' => $quantidade,
                'data_emprestimo' => $dados['data_emprestimo'] ?? now(),
                'data_prevista_devolucao' => $dados['data_prevista_devolucao'] ?? null,
                'estado' => 'ativo',
                'observacoes' => $dados['observacoes'] ?? null,
            ]);

            // Registar movimento de saída
            $this->registarSaida($materialId, $quantidade, [
                'observacoes' => 'Empréstimo #' . $emprestimo->id,
            ]);

            return $emprestimo;
        });
    }

    /**
     * Registar devolução de empréstimo
     */
    public function registarDevolucao(int $emprestimoId, ?int $quantidadeDevolvida = null): Emprestimo
    {
        return DB::transaction(function () use ($emprestimoId, $quantidadeDevolvida) {
            $emprestimo = Emprestimo::findOrFail($emprestimoId);

            if ($emprestimo->estado !== 'ativo') {
                throw new \Exception('Empréstimo já foi devolvido');
            }

            $quantidade = $quantidadeDevolvida ?? $emprestimo->quantidade;

            // Registar movimento de entrada
            $this->registarEntrada($emprestimo->material_id, $quantidade, [
                'observacoes' => 'Devolução de empréstimo #' . $emprestimo->id,
            ]);

            // Atualizar empréstimo
            $emprestimo->update([
                'data_devolucao' => now(),
                'quantidade_devolvida' => $quantidade,
                'estado' => 'devolvido',
            ]);

            return $emprestimo;
        });
    }

    /**
     * Obter materiais com stock baixo
     */
    public function materiaisStockBaixo(): array
    {
        $clubId = $this->clubContext->getActiveClubId();

        return Material::where('club_id', $clubId)
            ->whereColumn('quantidade_atual', '<=', 'quantidade_minima')
            ->with('armazem')
            ->get()
            ->toArray();
    }

    /**
     * Obter histórico de movimentos de um material
     */
    public function historicoMovimentos(int $materialId): array
    {
        $movimentos = MovimentoStock::where('material_id', $materialId)
            ->with(['fornecedor', 'centroCusto'])
            ->orderBy('data_movimento', 'desc')
            ->get();

        return $movimentos->toArray();
    }

    /**
     * Obter empréstimos ativos
     */
    public function emprestimosAtivos(): array
    {
        $clubId = $this->clubContext->getActiveClubId();

        return Emprestimo::where('club_id', $clubId)
            ->where('estado', 'ativo')
            ->with(['material', 'membro'])
            ->orderBy('data_emprestimo', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Obter empréstimos em atraso
     */
    public function emprestimosAtrasados(): array
    {
        $clubId = $this->clubContext->getActiveClubId();
        $hoje = Carbon::now();

        return Emprestimo::where('club_id', $clubId)
            ->where('estado', 'ativo')
            ->whereNotNull('data_prevista_devolucao')
            ->whereDate('data_prevista_devolucao', '<', $hoje)
            ->with(['material', 'membro'])
            ->orderBy('data_prevista_devolucao', 'asc')
            ->get()
            ->toArray();
    }
}
