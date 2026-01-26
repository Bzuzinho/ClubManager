<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MembroResource;
use App\Models\Membro;
use App\Services\Membros\MembroService;
use App\Services\Tenancy\ClubContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Controller para gestão de membros (Nova versão v2)
 */
class MembrosController extends Controller
{
    protected MembroService $membroService;
    protected ClubContext $clubContext;

    public function __construct(MembroService $membroService, ClubContext $clubContext)
    {
        $this->membroService = $membroService;
        $this->clubContext = $clubContext;
    }

    /**
     * Listar membros do clube ativo
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Membro::class);

        // ClubScope já filtra automaticamente por club_id
        $query = Membro::with(['user.dadosPessoais', 'dadosFinanceiros', 'atleta']);

        // Filtros
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $membros = $query->paginate($request->per_page ?? 15);

        return MembroResource::collection($membros);
    }

    /**
     * Criar novo membro
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Membro::class);

        $request->validate([
            'user.name' => 'required|string|max:255',
            'user.email' => 'nullable|email|unique:users,email',
            'dados_pessoais.nome_completo' => 'required|string|max:255',
            'dados_pessoais.data_nascimento' => 'nullable|date',
            'tipos_utilizador' => 'required|array',
            'tipos_utilizador.*' => 'exists:tipos_utilizador,id',
        ]);

        try {
            $membro = $this->membroService->criarMembro($request->all());

            return (new MembroResource($membro->load([
                'user.dadosPessoais', 
                'dadosFinanceiros', 
                'atleta'
            ])))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao criar membro',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Obter detalhes de um membro
     */
    public function show(int $id): MembroResource
    {
        // ClubScope já filtra automaticamente por club_id
        $membro = Membro::with([
            'user.dadosPessoais',
            'user.encarregadosEducacao.dadosPessoais',
            'user.educandos.dadosPessoais',
            'dadosFinanceiros.mensalidade',
            'dadosFinanceiros.centrosCusto',
            'atleta',
            'tiposUtilizador',
            'club',
        ])
        ->findOrFail($id);

        $this->authorize('view', $membro);

        return new MembroResource($membro);
    }

    /**
     * Atualizar membro
     */
    public function update(Request $request, int $id): JsonResponse
    {
        // ClubScope já filtra automaticamente por club_id
        $membro = Membro::findOrFail($id);

        $this->authorize('update', $membro);

        $request->validate([
            'user.name' => 'sometimes|string|max:255',
            'user.email' => 'sometimes|email|unique:users,email,' . $membro->user_id,
            'dados_pessoais.nome_completo' => 'sometimes|string|max:255',
            'estado' => 'sometimes|in:ativo,inativo,suspenso',
            'numero_socio' => 'sometimes|string|unique:membros,numero_socio,' . $id . ',id,club_id,' . $membro->club_id,
        ]);

        try {
            $membroAtualizado = $this->membroService->atualizarMembro($id, $request->all());

            return response()->json([
                'message' => 'Membro atualizado com sucesso',
                'data' => new MembroResource($membroAtualizado->load([
                    'user.dadosPessoais', 
                    'dadosFinanceiros', 
                    'atleta'
                ])),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao atualizar membro',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Desativar membro (soft state change)
     */
    public function destroy(int $id): JsonResponse
    {
        // ClubScope já filtra automaticamente por club_id
        $membro = Membro::findOrFail($id);

        $this->authorize('delete', $membro);
        
        $membro->update([
            'estado' => 'inativo',
            'data_fim' => now(),
        ]);

        return response()->json([
            'message' => 'Membro desativado com sucesso',
        ]);
    }
}
