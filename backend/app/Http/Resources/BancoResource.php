<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BancoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'club_id' => $this->club_id,
            'nome' => $this->nome,
            'iban' => $this->iban,
            'swift_bic' => $this->swift_bic,
            'ativo' => $this->ativo,
        ];
    }
}
