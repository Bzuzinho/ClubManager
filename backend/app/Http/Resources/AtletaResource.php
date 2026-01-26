<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AtletaResource extends JsonResource
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
            'membro_id' => $this->membro_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relacionamentos
            'membro' => new MembroResource($this->whenLoaded('membro')),
            'dados_desportivos' => new DadosDesportivosResource($this->whenLoaded('dadosDesportivos')),
            'escaloes' => AtletaEscalaoResource::collection($this->whenLoaded('escaloes')),
            'resultados' => ResultadoResource::collection($this->whenLoaded('resultados')),
        ];
    }
}
