<?php

namespace App\Models\Traits;

use App\Models\Scopes\ClubScope;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait para models que pertencem a um clube (multi-tenancy)
 * 
 * Aplica automaticamente o ClubScope em todas as queries
 * Garante isolamento de dados entre clubes
 * 
 * Uso:
 * class Membro extends Model
 * {
 *     use HasClubScope;
 * }
 * 
 * Para queries sem scope (admin):
 * Membro::withoutGlobalScope(ClubScope::class)->get();
 */
trait HasClubScope
{
    /**
     * Boot do trait - adiciona o global scope
     */
    protected static function bootHasClubScope(): void
    {
        static::addGlobalScope(new ClubScope());
    }

    /**
     * Scope para queries sem filtro de clube (uso administrativo)
     * 
     * Uso: Model::allClubs()->get()
     */
    public function scopeAllClubs(Builder $query): Builder
    {
        return $query->withoutGlobalScope(ClubScope::class);
    }

    /**
     * Scope para filtrar por clube específico
     * 
     * Uso: Model::forClub($clubId)->get()
     */
    public function scopeForClub(Builder $query, int $clubId): Builder
    {
        return $query->withoutGlobalScope(ClubScope::class)
            ->where($this->getTable() . '.club_id', $clubId);
    }
}
