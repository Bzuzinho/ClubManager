<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PagamentoResource extends JsonResource
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
            'fatura_id' => $this->fatura_id,
            'data_pagamento' => $this->data_pagamento?->format('Y-m-d'),
            'valor' => (float) $this->valor,
            'metodo' => $this->metodo,
            'referencia' => $this->referencia,
            'observacoes' => $this->observacoes,
            'ficheiro_comprovativo_id' => $this->ficheiro_comprovativo_id,
            'registado_por' => $this->registado_por,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relacionamentos
            'fatura' => new FaturaResource($this->whenLoaded('fatura')),
        ];
    }
}
