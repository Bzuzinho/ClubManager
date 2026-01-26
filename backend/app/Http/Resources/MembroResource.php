<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembroResource extends JsonResource
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
            'numero_socio' => $this->numero_socio,
            'estado' => $this->estado,
            'data_adesao' => $this->data_adesao?->format('Y-m-d'),
            'data_fim' => $this->data_fim?->format('Y-m-d'),
            'observacoes' => $this->observacoes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relacionamentos (quando carregados)
            'user' => new UserResource($this->whenLoaded('user')),
            'club' => new ClubResource($this->whenLoaded('club')),
            'dados_financeiros' => new DadosFinanceirosResource($this->whenLoaded('dadosFinanceiros')),
            'atleta' => new AtletaResource($this->whenLoaded('atleta')),
            'tipos_utilizador' => TipoUtilizadorResource::collection($this->whenLoaded('tiposUtilizador')),
            
            // Contadores (quando disponíveis)
            'faturas_count' => $this->when(isset($this->faturas_count), $this->faturas_count),
            'presencas_count' => $this->when(isset($this->presencas_count), $this->presencas_count),
        ];
    }
}
