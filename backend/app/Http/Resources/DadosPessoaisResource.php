<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DadosPessoaisResource extends JsonResource
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
            'user_id' => $this->user_id,
            'foto_perfil' => $this->foto_perfil,
            'nome_completo' => $this->nome_completo,
            'data_nascimento' => $this->data_nascimento?->format('Y-m-d'),
            'nif' => $this->nif,
            'cc' => $this->cc,
            'morada' => $this->morada,
            'codigo_postal' => $this->codigo_postal,
            'localidade' => $this->localidade,
            'nacionalidade' => $this->nacionalidade,
            'estado_civil' => $this->estado_civil,
            'ocupacao' => $this->ocupacao,
            'empresa' => $this->empresa,
            'escola' => $this->escola,
            'sexo' => $this->sexo,
            'menor' => $this->menor,
            'numero_irmaos' => $this->numero_irmaos,
            'contacto_telefonico' => $this->contacto_telefonico,
            'email_secundario' => $this->email_secundario,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
