<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Membro;
use App\Services\Tenancy\ClubContext;
use Illuminate\Auth\Access\HandlesAuthorization;

class MembroPolicy
{
    use HandlesAuthorization;

    protected ClubContext $clubContext;

    public function __construct(ClubContext $clubContext)
    {
        $this->clubContext = $clubContext;
    }

    /**
     * Determina se o utilizador pode ver qualquer membro
     */
    public function viewAny(User $user): bool
    {
        // Todos os utilizadores autenticados podem listar membros do seu clube
        return true; // O middleware EnsureClubContext já garante que tem clube ativo
    }

    /**
     * Determina se o utilizador pode ver um membro específico
     */
    public function view(User $user, Membro $membro): bool
    {
        // O ClubScope já filtra automaticamente, então se encontrou o membro, tem acesso
        // Verificação adicional: confirmar que o user tem acesso ao clube do membro
        $activeClubId = $this->clubContext->getActiveClubId();
        
        if (!$activeClubId || $membro->club_id !== $activeClubId) {
            return false;
        }

        return $this->clubContext->userHasAccessToClub($membro->club_id);
    }

    /**
     * Determina se o utilizador pode criar membros
     */
    public function create(User $user): bool
    {
        return true; // Se tem acesso ao clube, pode criar membros
    }

    /**
     * Determina se o utilizador pode atualizar um membro
     */
    public function update(User $user, Membro $membro): bool
    {
        $activeClubId = $this->clubContext->getActiveClubId();
        
        if (!$activeClubId || $membro->club_id !== $activeClubId) {
            return false;
        }

        return $this->clubContext->userHasAccessToClub($membro->club_id);
    }

    /**
     * Determina se o utilizador pode eliminar um membro
     */
    public function delete(User $user, Membro $membro): bool
    {
        $activeClubId = $this->clubContext->getActiveClubId();
        
        if (!$activeClubId || $membro->club_id !== $activeClubId) {
            return false;
        }

        return $this->clubContext->userHasAccessToClub($membro->club_id);
    }

    /**
     * Determina se o utilizador pode gerir documentos de um membro
     */
    public function manageDocuments(User $user, Membro $membro): bool
    {
        $activeClubId = $this->clubContext->getActiveClubId();
        
        if (!$activeClubId || $membro->club_id !== $activeClubId) {
            return false;
        }

        return $this->clubContext->userHasAccessToClub($membro->club_id);
    }

    /**
     * Determina se o utilizador pode ver dados financeiros de um membro
     */
    public function viewFinancial(User $user, Membro $membro): bool
    {
        $activeClubId = $this->clubContext->getActiveClubId();
        
        if (!$activeClubId || $membro->club_id !== $activeClubId) {
            return false;
        }

        return $this->clubContext->userHasAccessToClub($membro->club_id);
    }
}
