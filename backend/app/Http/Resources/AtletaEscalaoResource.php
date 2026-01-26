<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AtletaEscalaoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'atleta_id' => $this->atleta_id,
            'escalao_id' => $this->escalao_id,
            'epoca_id' => $this->epoca_id,
            'data_inicio' => $this->data_inicio?->format('Y-m-d'),
            'data_fim' => $this->data_fim?->format('Y-m-d'),
        ];
    }
}
