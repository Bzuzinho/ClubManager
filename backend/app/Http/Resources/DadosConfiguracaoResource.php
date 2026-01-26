<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DadosConfiguracaoResource extends JsonResource
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
            'user_id' => $this->user_id,
            'rgpd' => $this->rgpd,
            'rgpd_assinado' => $this->rgpd_assinado,
            'data_rgpd' => $this->data_rgpd?->format('Y-m-d'),
            'arquivo_rgpd' => $this->arquivo_rgpd,
            'consentimento' => $this->consentimento,
            'data_consentimento' => $this->data_consentimento?->format('Y-m-d'),
            'arquivo_consentimento' => $this->arquivo_consentimento,
            'afiliacao' => $this->afiliacao,
            'data_afiliacao' => $this->data_afiliacao?->format('Y-m-d'),
            'arquivo_afiliacao' => $this->arquivo_afiliacao,
            'declaracao_transporte' => $this->declaracao_transporte,
            'declaracao_transporte_arquivo' => $this->declaracao_transporte_arquivo,
            'email_utilizador' => $this->email_utilizador,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
