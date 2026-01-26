<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Tenancy\ClubContext;

/**
 * Middleware para validar contexto de clube
 * Garante que há um clube ativo na sessão
 */
class EnsureClubContext
{
    protected ClubContext $clubContext;

    public function __construct(ClubContext $clubContext)
    {
        $this->clubContext = $clubContext;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $club = $this->clubContext->requireActiveClub();
            
            // Adicionar club_id ao request para fácil acesso
            $request->merge(['active_club_id' => $club->id]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Clube não selecionado',
                'message' => $e->getMessage(),
            ], 400);
        }

        return $next($request);
    }
}
