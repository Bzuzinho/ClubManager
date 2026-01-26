<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DadosFinanceirosResource extends JsonResource
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
            'mensalidade_id' => $this->mensalidade_id,
            'banco_id' => $this->banco_id,
            'iban' => $this->iban,
            'dia_pagamento' => $this->dia_pagamento,
            'observacoes' => $this->observacoes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relacionamentos
            'mensalidade' => new MensalidadeResource($this->whenLoaded('mensalidade')),
            'banco' => new BancoResource($this->whenLoaded('banco')),
        ];
    }
}
