<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaturaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'club_id' => $this->club_id,
            'membro_id' => $this->membro_id,
            'data_emissao' => $this->data_emissao?->format('Y-m-d'),
            'mes' => $this->mes,
            'data_inicio_periodo' => $this->data_inicio_periodo?->format('Y-m-d'),
            'data_fim_periodo' => $this->data_fim_periodo?->format('Y-m-d'),
            'valor_total' => (float) $this->valor_total,
            'status_cache' => $this->status_cache,
            'numero_recibo' => $this->numero_recibo,
            'referencia_pagamento' => $this->referencia_pagamento,
            'centro_custo_id' => $this->centro_custo_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Estado derivado (calculado)
            'estado_pagamento' => $this->getEstadoPagamento(),
            'valor_pago' => $this->getValorPago(),
            'valor_pendente' => $this->getValorPendente(),
            
            // Relacionamentos
            'membro' => new MembroResource($this->whenLoaded('membro')),
            'itens' => FaturaItemResource::collection($this->whenLoaded('itens')),
            'pagamentos' => PagamentoResource::collection($this->whenLoaded('pagamentos')),
            'centro_custo' => new CentroCustoResource($this->whenLoaded('centroCusto')),
            
            // Contadores
            'itens_count' => $this->when(isset($this->itens_count), $this->itens_count),
            'pagamentos_count' => $this->when(isset($this->pagamentos_count), $this->pagamentos_count),
        ];
    }

    /**
     * Calcula estado de pagamento
     */
    protected function getEstadoPagamento(): string
    {
        if (!$this->relationLoaded('pagamentos')) {
            return $this->status_cache ?? 'pendente';
        }

        $valorPago = $this->pagamentos->sum('valor');
        $valorTotal = $this->valor_total;

        if ($valorPago >= $valorTotal) {
            return 'pago';
        }

        if ($valorPago > 0) {
            return 'parcial';
        }

        // Verificar se está em atraso (data_vencimento implementar quando houver)
        return 'pendente';
    }

    /**
     * Calcula valor pago
     */
    protected function getValorPago(): float
    {
        if (!$this->relationLoaded('pagamentos')) {
            return 0.0;
        }

        return (float) $this->pagamentos->sum('valor');
    }

    /**
     * Calcula valor pendente
     */
    protected function getValorPendente(): float
    {
        return max(0, $this->valor_total - $this->getValorPago());
    }
}
