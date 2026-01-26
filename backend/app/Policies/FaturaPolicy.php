<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Fatura;
use Illuminate\Auth\Access\HandlesAuthorization;

class FaturaPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o utilizador pode ver qualquer fatura
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('financeiro.view') || $user->hasRole('admin');
    }

    /**
     * Determina se o utilizador pode ver uma fatura específica
     */
    public function view(User $user, Fatura $fatura): bool
    {
        // Verifica se a fatura pertence ao clube do utilizador
        if ($fatura->club_id !== $user->club_id) {
            return false;
        }

        return $user->hasPermissionTo('financeiro.view') || $user->hasRole('admin');
    }

    /**
     * Determina se o utilizador pode criar faturas
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('financeiro.create') || $user->hasRole('admin');
    }

    /**
     * Determina se o utilizador pode atualizar uma fatura
     */
    public function update(User $user, Fatura $fatura): bool
    {
        // Verifica se a fatura pertence ao clube do utilizador
        if ($fatura->club_id !== $user->club_id) {
            return false;
        }

        // Não permite editar faturas pagas
        if ($fatura->status_cache === 'paga') {
            return false;
        }

        return $user->hasPermissionTo('financeiro.update') || $user->hasRole('admin');
    }

    /**
     * Determina se o utilizador pode eliminar uma fatura
     */
    public function delete(User $user, Fatura $fatura): bool
    {
        // Verifica se a fatura pertence ao clube do utilizador
        if ($fatura->club_id !== $user->club_id) {
            return false;
        }

        // Não permite eliminar faturas pagas
        if ($fatura->status_cache === 'paga') {
            return false;
        }

        return $user->hasPermissionTo('financeiro.delete') || $user->hasRole('admin');
    }

    /**
     * Determina se o utilizador pode gerar mensalidades
     */
    public function generateMensalidades(User $user): bool
    {
        return $user->hasPermissionTo('financeiro.generate') || $user->hasRole('admin');
    }

    /**
     * Determina se o utilizador pode anular uma fatura
     */
    public function cancel(User $user, Fatura $fatura): bool
    {
        if ($fatura->club_id !== $user->club_id) {
            return false;
        }

        return $user->hasPermissionTo('financeiro.cancel') || $user->hasRole('admin');
    }
}
