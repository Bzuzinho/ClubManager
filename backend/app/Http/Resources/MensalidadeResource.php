<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MensalidadeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'club_id' => $this->club_id,
            'nome' => $this->nome,
            'escalao_id' => $this->escalao_id,
            'valor' => (float) $this->valor,
            'ativo' => $this->ativo,
        ];
    }
}
