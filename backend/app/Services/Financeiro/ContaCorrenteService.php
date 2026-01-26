<?php

namespace App\Services\Financeiro;

use App\Models\Fatura;
use App\Models\Pagamento;
use App\Models\Membro;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Service para gestão de conta corrente
 * Estado derivado conforme especificação
 */
class ContaCorrenteService
{
    /**
     * Obter conta corrente de um membro
     * 
     * @param int $membroId
     * @return array
     */
    public function contaCorrente(int $membroId): array
    {
        $faturas = Fatura::with(['pagamentos', 'itens'])
            ->where('membro_id', $membroId)
            ->orderBy('data_emissao')
            ->get();

        $linhas = [];
        $saldoAcumulado = 0;

        foreach ($faturas as $fatura) {
            $totalPago = $fatura->pagamentos->sum('valor');
            $saldoFatura = $fatura->valor_total - $totalPago;
            $saldoAcumulado += $saldoFatura;

            $linhas[] = [
                'tipo' => 'fatura',
                'data' => $fatura->data_emissao,
                'mes' => $fatura->mes,
                'descricao' => 'Fatura #' . $fatura->id . ' - ' . $fatura->mes,
                'debito' => $fatura->valor_total,
                'credito' => 0,
                'saldo_fatura' => $saldoFatura,
                'saldo_acumulado' => $saldoAcumulado,
                'estado' => $this->calcularEstadoFatura($fatura),
                'fatura_id' => $fatura->id,
            ];

            // Adicionar pagamentos
            foreach ($fatura->pagamentos as $pagamento) {
                $linhas[] = [
                    'tipo' => 'pagamento',
                    'data' => $pagamento->data_pagamento,
                    'mes' => null,
                    'descricao' => 'Pagamento - ' . $pagamento->metodo,
                    'debito' => 0,
                    'credito' => $pagamento->valor,
                    'saldo_fatura' => null,
                    'saldo_acumulado' => $saldoAcumulado,
                    'estado' => null,
                    'pagamento_id' => $pagamento->id,
                ];
            }
        }

        return [
            'linhas' => $linhas,
            'saldo_total' => $saldoAcumulado,
            'total_em_atraso' => $this->calcularTotalAtraso($faturas),
            'proxima_fatura' => $this->calcularProximaFatura($faturas),
        ];
    }

    /**
     * Calcular estado de uma fatura (derivado)
     */
    protected function calcularEstadoFatura(Fatura $fatura): string
    {
        $totalPago = $fatura->pagamentos->sum('valor');
        
        // Pago
        if ($totalPago >= $fatura->valor_total) {
            return 'pago';
        }
        
        // Parcial
        if ($totalPago > 0 && $totalPago < $fatura->valor_total) {
            return 'parcial';
        }
        
        // Atraso (venceu)
        $hoje = Carbon::now();
        $dataVencimento = Carbon::parse($fatura->data_emissao)->addMonth();
        
        if ($hoje->gt($dataVencimento)) {
            return 'atraso';
        }
        
        // Pendente
        return 'pendente';
    }

    /**
     * Calcular total em atraso
     */
    protected function calcularTotalAtraso(Collection $faturas): float
    {
        $total = 0;
        $hoje = Carbon::now();

        foreach ($faturas as $fatura) {
            $totalPago = $fatura->pagamentos->sum('valor');
            $saldo = $fatura->valor_total - $totalPago;

            if ($saldo > 0) {
                $dataVencimento = Carbon::parse($fatura->data_emissao)->addMonth();
                if ($hoje->gt($dataVencimento)) {
                    $total += $saldo;
                }
            }
        }

        return $total;
    }

    /**
     * Calcular próxima fatura esperada
     */
    protected function calcularProximaFatura(Collection $faturas): ?array
    {
        if ($faturas->isEmpty()) {
            return null;
        }

        $ultimaFatura = $faturas->last();
        $proximoMes = Carbon::parse($ultimaFatura->mes . '-01')->addMonth();

        return [
            'mes' => $proximoMes->format('Y-m'),
            'previsao_emissao' => $proximoMes->format('Y-m-d'),
        ];
    }

    /**
     * Obter resumo financeiro de um membro
     */
    public function resumoFinanceiro(int $membroId): array
    {
        $faturas = Fatura::with('pagamentos')
            ->where('membro_id', $membroId)
            ->get();

        $totalFaturado = $faturas->sum('valor_total');
        $totalPago = 0;
        
        foreach ($faturas as $fatura) {
            $totalPago += $fatura->pagamentos->sum('valor');
        }

        $saldoDevedor = $totalFaturado - $totalPago;
        $totalEmAtraso = $this->calcularTotalAtraso($faturas);

        return [
            'total_faturado' => $totalFaturado,
            'total_pago' => $totalPago,
            'saldo_devedor' => $saldoDevedor,
            'total_em_atraso' => $totalEmAtraso,
            'numero_faturas' => $faturas->count(),
            'numero_faturas_pagas' => $faturas->filter(function($f) {
                return $f->pagamentos->sum('valor') >= $f->valor_total;
            })->count(),
            'numero_faturas_pendentes' => $faturas->filter(function($f) {
                return $f->pagamentos->sum('valor') < $f->valor_total;
            })->count(),
        ];
    }

    /**
     * Registar pagamento
     */
    public function registarPagamento(int $faturaId, array $dadosPagamento): Pagamento
    {
        $fatura = Fatura::findOrFail($faturaId);

        $pagamento = Pagamento::create([
            'club_id' => $fatura->club_id,
            'fatura_id' => $faturaId,
            'data_pagamento' => $dadosPagamento['data_pagamento'] ?? now(),
            'valor' => $dadosPagamento['valor'],
            'metodo' => $dadosPagamento['metodo'],
            'referencia' => $dadosPagamento['referencia'] ?? null,
            'banco_id' => $dadosPagamento['banco_id'] ?? null,
            'ficheiro_comprovativo_id' => $dadosPagamento['ficheiro_comprovativo_id'] ?? null,
        ]);

        // Atualizar status_cache da fatura
        $this->atualizarStatusFatura($fatura);

        return $pagamento;
    }

    /**
     * Atualizar status cache da fatura
     */
    protected function atualizarStatusFatura(Fatura $fatura): void
    {
        $estado = $this->calcularEstadoFatura($fatura);
        $fatura->update(['status_cache' => $estado]);
    }
}
