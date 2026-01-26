<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatalogoFaturaItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'club_id' => $this->club_id,
            'tipo' => $this->tipo,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'valor_default' => (float) $this->valor_default,
            'ativo' => $this->ativo,
        ];
    }
}
