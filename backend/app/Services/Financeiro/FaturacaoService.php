<?php

namespace App\Services\Financeiro;

use App\Models\Fatura;
use App\Models\FaturaItem;
use App\Models\Membro;
use App\Models\CatalogoFaturaItem;
use App\Services\Tenancy\ClubContext;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Service para faturação automática de mensalidades
 * Conforme especificação
 */
class FaturacaoService
{
    protected ClubContext $clubContext;

    public function __construct(ClubContext $clubContext)
    {
        $this->clubContext = $clubContext;
    }

    /**
     * Gerar faturas de mensalidade para um membro
     * 
     * @param int $membroId ID do membro
     * @param string $mesInicio Mês de início (YYYY-MM)
     * @param string|null $mesFim Mês de fim (YYYY-MM), null = até Julho
     * @return array Faturas criadas
     */
    public function gerarFaturasMensalidade(int $membroId, string $mesInicio, ?string $mesFim = null): array
    {
        return DB::transaction(function () use ($membroId, $mesInicio, $mesFim) {
            $clubId = $this->clubContext->getActiveClubId();
            
            if (!$clubId) {
                throw new \Exception('Clube não definido');
            }

            $membro = Membro::with('dadosFinanceiros.mensalidade')
                ->where('id', $membroId)
                ->where('club_id', $clubId)
                ->firstOrFail();

            if (!$membro->dadosFinanceiros || !$membro->dadosFinanceiros->mensalidade) {
                throw new \Exception('Membro não tem mensalidade configurada');
            }

            $mensalidade = $membro->dadosFinanceiros->mensalidade;

            // Determinar mês fim (default: Julho do ano corrente)
            if (!$mesFim) {
                $anoInicio = Carbon::parse($mesInicio)->year;
                $mesFim = $anoInicio . '-07';
            }

            $inicio = Carbon::parse($mesInicio . '-01');
            $fim = Carbon::parse($mesFim . '-01');

            $faturas = [];

            while ($inicio <= $fim) {
                $mes = $inicio->format('Y-m');

                // Verificar se já existe fatura para este mês
                $faturaExistente = Fatura::where('club_id', $clubId)
                    ->where('membro_id', $membroId)
                    ->where('mes', $mes)
                    ->first();

                if (!$faturaExistente) {
                    $fatura = $this->criarFaturaMes($clubId, $membro, $mensalidade, $mes);
                    $faturas[] = $fatura;
                }

                $inicio->addMonth();
            }

            return $faturas;
        });
    }

    /**
     * Criar fatura para um mês específico
     */
    protected function criarFaturaMes($clubId, Membro $membro, $mensalidade, string $mes): Fatura
    {
        $dataEmissao = Carbon::parse($mes . '-01');
        $diaCobranca = $membro->dadosFinanceiros->dia_cobranca ?? 1;
        
        $dataInicioPeriodo = $dataEmissao->copy()->day($diaCobranca);
        $dataFimPeriodo = $dataInicioPeriodo->copy()->addMonth()->subDay();

        // Criar fatura
        $fatura = Fatura::create([
            'club_id' => $clubId,
            'membro_id' => $membro->id,
            'data_emissao' => $dataEmissao,
            'mes' => $mes,
            'data_inicio_periodo' => $dataInicioPeriodo,
            'data_fim_periodo' => $dataFimPeriodo,
            'valor_total' => 0, // Será calculado depois
            'status_cache' => 'pendente',
        ]);

        // Criar item de mensalidade
        $item = FaturaItem::create([
            'club_id' => $clubId,
            'fatura_id' => $fatura->id,
            'descricao' => $mensalidade->nome . ' - ' . $mes,
            'valor_unitario' => $mensalidade->valor,
            'quantidade' => 1,
            'imposto_percentual' => 0,
            'total_linha' => $mensalidade->valor,
        ]);

        // Atualizar valor total da fatura
        $fatura->update([
            'valor_total' => $mensalidade->valor
        ]);

        return $fatura;
    }

    /**
     * Adicionar item extra a uma fatura
     */
    public function adicionarItemFatura(int $faturaId, array $dadosItem): FaturaItem
    {
        return DB::transaction(function () use ($faturaId, $dadosItem) {
            $fatura = Fatura::findOrFail($faturaId);

            $valorUnitario = $dadosItem['valor_unitario'] ?? 0;
            $quantidade = $dadosItem['quantidade'] ?? 1;
            $impostoPercent = $dadosItem['imposto_percentual'] ?? 0;

            $totalLinha = $valorUnitario * $quantidade;
            if ($impostoPercent > 0) {
                $totalLinha += $totalLinha * ($impostoPercent / 100);
            }

            $item = FaturaItem::create([
                'club_id' => $fatura->club_id,
                'fatura_id' => $fatura->id,
                'catalogo_item_id' => $dadosItem['catalogo_item_id'] ?? null,
                'descricao' => $dadosItem['descricao'],
                'valor_unitario' => $valorUnitario,
                'quantidade' => $quantidade,
                'imposto_percentual' => $impostoPercent,
                'total_linha' => $totalLinha,
                'centro_custo_id' => $dadosItem['centro_custo_id'] ?? null,
            ]);

            // Recalcular total da fatura
            $this->recalcularTotalFatura($fatura);

            return $item;
        });
    }

    /**
     * Recalcular total de uma fatura
     */
    protected function recalcularTotalFatura(Fatura $fatura): void
    {
        $total = $fatura->itens()->sum('total_linha');
        $fatura->update(['valor_total' => $total]);
    }

    /**
     * Criar fatura avulsa (não mensalidade)
     */
    public function criarFaturaAvulsa(int $membroId, array $itens, array $dadosFatura = []): Fatura
    {
        return DB::transaction(function () use ($membroId, $itens, $dadosFatura) {
            $clubId = $this->clubContext->getActiveClubId();

            $fatura = Fatura::create([
                'club_id' => $clubId,
                'membro_id' => $membroId,
                'data_emissao' => $dadosFatura['data_emissao'] ?? now(),
                'mes' => $dadosFatura['mes'] ?? now()->format('Y-m'),
                'data_inicio_periodo' => $dadosFatura['data_inicio_periodo'] ?? null,
                'data_fim_periodo' => $dadosFatura['data_fim_periodo'] ?? null,
                'valor_total' => 0,
                'status_cache' => 'pendente',
                'centro_custo_id' => $dadosFatura['centro_custo_id'] ?? null,
            ]);

            // Adicionar itens
            foreach ($itens as $itemData) {
                $this->adicionarItemFatura($fatura->id, $itemData);
            }

            return $fatura->fresh();
        });
    }
}
