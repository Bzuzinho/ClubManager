<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaturaItemResource extends JsonResource
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
            'fatura_id' => $this->fatura_id,
            'catalogo_item_id' => $this->catalogo_item_id,
            'descricao' => $this->descricao,
            'quantidade' => (float) $this->quantidade,
            'valor_unitario' => (float) $this->valor_unitario,
            'valor_total' => (float) $this->valor_total,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relacionamentos
            'catalogo_item' => new CatalogoFaturaItemResource($this->whenLoaded('catalogoItem')),
        ];
    }
}
