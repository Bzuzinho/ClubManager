<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Global Scope para filtrar automaticamente por club_id
 * 
 * Garante multi-tenancy: queries automaticamente filtram pelo clube ativo
 * Previne data leakage entre clubes
 * 
 * Uso:
 * - Adicionar trait HasClubScope aos models que pertencem a um clube
 * - O scope será aplicado automaticamente em todas as queries
 */
class ClubScope implements Scope
{
    /**
     * Aplica o scope à query
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Obter clube ativo do contexto
        $clubContext = app(\App\Services\Tenancy\ClubContext::class);
        $activeClubId = $clubContext->getActiveClubId();

        // Apenas filtrar se houver clube ativo
        if ($activeClubId) {
            $builder->where($model->getTable() . '.club_id', $activeClubId);
        }
    }
}
