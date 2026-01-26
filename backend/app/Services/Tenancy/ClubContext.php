<?php

namespace App\Services\Tenancy;

use App\Models\Club;
use Illuminate\Support\Facades\Session;

/**
 * Service para gestão de contexto multi-clube (tenancy)
 */
class ClubContext
{
    /**
     * Obter o clube ativo da sessão ou do header X-Club-Id
     */
    public function getActiveClub(): ?Club
    {
        // Tentar obter do header primeiro (para API stateless)
        $clubId = request()->header('X-Club-Id');
        
        // Se não houver no header, tentar na sessão (para web)
        if (!$clubId) {
            $clubId = Session::get('active_club_id');
        }
        
        if (!$clubId) {
            return null;
        }
        
        return Club::find($clubId);
    }

    /**
     * Obter o ID do clube ativo
     */
    public function getActiveClubId(): ?int
    {
        // Tentar obter do header primeiro (para API stateless)
        $clubId = request()->header('X-Club-Id');
        
        // Se não houver no header, tentar na sessão (para web)
        if (!$clubId) {
            $clubId = Session::get('active_club_id');
        }
        
        return $clubId ? (int) $clubId : null;
    }

    /**
     * Definir o clube ativo
     */
    public function setActiveClub(int $clubId): void
    {
        // Verificar se o user tem acesso a este clube
        $user = auth()->user();
        
        if (!$user) {
            throw new \Exception('Utilizador não autenticado');
        }

        $hasAccess = $user->clubUsers()
            ->where('club_id', $clubId)
            ->whereRaw('ativo = true')
            ->exists();

        if (!$hasAccess) {
            throw new \Exception('Utilizador não tem acesso a este clube');
        }

        Session::put('active_club_id', $clubId);
    }

    /**
     * Limpar o clube ativo
     */
    public function clearActiveClub(): void
    {
        Session::forget('active_club_id');
    }

    /**
     * Obter todos os clubes do utilizador autenticado
     */
    public function getUserClubs(): array
    {
        $user = auth()->user();
        
        if (!$user) {
            return [];
        }

        return $user->clubUsers()
            ->whereRaw('ativo = true')
            ->with('club')
            ->get()
            ->pluck('club')
            ->toArray();
    }

    /**
     * Verificar se o utilizador tem acesso ao clube
     */
    public function userHasAccessToClub(int $clubId): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }

        return $user->clubUsers()
            ->where('club_id', $clubId)
            ->whereRaw('ativo = true')
            ->exists();
    }

    /**
     * Garantir que há um clube ativo, ou lançar exceção
     */
    public function requireActiveClub(): Club
    {
        $club = $this->getActiveClub();
        
        if (!$club) {
            throw new \Exception('Nenhum clube ativo. Por favor selecione um clube.');
        }
        
        return $club;
    }
}
