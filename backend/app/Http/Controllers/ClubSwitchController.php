<?php

namespace App\Http\Controllers;

use App\Services\Tenancy\ClubContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller para seleção e troca de clubes
 */
class ClubSwitchController extends Controller
{
    protected ClubContext $clubContext;

    public function __construct(ClubContext $clubContext)
    {
        $this->clubContext = $clubContext;
    }

    /**
     * Listar clubes do utilizador autenticado
     */
    public function index(): JsonResponse
    {
        $clubs = $this->clubContext->getUserClubs();
        $activeClub = $this->clubContext->getActiveClub();

        return response()->json([
            'clubs' => $clubs,
            'active_club_id' => $activeClub?->id,
        ]);
    }

    /**
     * Selecionar clube ativo
     */
    public function switch(Request $request): JsonResponse
    {
        $request->validate([
            'club_id' => 'required|integer|exists:clubs,id',
        ]);

        try {
            $this->clubContext->setActiveClub($request->club_id);

            return response()->json([
                'message' => 'Clube selecionado com sucesso',
                'club' => $this->clubContext->getActiveClub(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao selecionar clube',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Obter clube ativo
     */
    public function active(): JsonResponse
    {
        $activeClub = $this->clubContext->getActiveClub();

        if (!$activeClub) {
            return response()->json([
                'message' => 'Nenhum clube ativo',
            ], 404);
        }

        return response()->json([
            'club' => $activeClub,
        ]);
    }

    /**
     * Limpar seleção de clube
     */
    public function clear(): JsonResponse
    {
        $this->clubContext->clearActiveClub();

        return response()->json([
            'message' => 'Clube deselecionado',
        ]);
    }
}
