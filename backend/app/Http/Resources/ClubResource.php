<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClubResource extends JsonResource
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
            'nome_fiscal' => $this->nome_fiscal,
            'abreviatura' => $this->abreviatura,
            'nif' => $this->nif,
            'morada' => $this->morada,
            'contacto_telefonico' => $this->contacto_telefonico,
            'email' => $this->email,
            'logo_ficheiro_id' => $this->logo_ficheiro_id,
            'ativo' => $this->ativo,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
