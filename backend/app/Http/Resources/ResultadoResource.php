<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResultadoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'clube_id' => $this->club_id,
            'evento_id' => $this->evento_id,
            'atleta_id' => $this->atleta_id,
            'prova_id' => $this->prova_id,
            'tempo' => $this->tempo,
            'posicao' => $this->posicao,
            'observacoes' => $this->observacoes,
        ];
    }
}
