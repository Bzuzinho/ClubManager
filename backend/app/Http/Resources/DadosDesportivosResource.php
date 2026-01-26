<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DadosDesportivosResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'club_id' => $this->club_id,
            'atleta_id' => $this->atleta_id,
            'num_federacao' => $this->num_federacao,
            'cartao_federacao' => $this->cartao_federacao,
            'numero_pmb' => $this->numero_pmb,
            'data_inscricao' => $this->data_inscricao?->format('Y-m-d'),
            'inscricao' => $this->inscricao,
            'escalao_atual_id' => $this->escalao_atual_id,
            'data_atestado_medico' => $this->data_atestado_medico?->format('Y-m-d'),
            'informacoes_medicas' => $this->informacoes_medicas,
            'ativo' => $this->ativo,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
