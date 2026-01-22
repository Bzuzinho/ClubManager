<?php

namespace App\Http\Controllers;

use App\Models\Equipa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EquipaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Equipa::with(['modalidade', 'escalao', 'treinadorPrincipal.pessoa']);

        // Filtros
        if ($request->has('modalidade_id')) {
            $query->where('modalidade_id', $request->modalidade_id);
        }

        if ($request->has('escalao_id')) {
            $query->where('escalao_id', $request->escalao_id);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('temporada')) {
            $query->where('temporada', $request->temporada);
        } elseif ($request->get('temporada_atual') === 'true') {
            $query->temporadaAtual();
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'ilike', "%{$search}%")
                  ->orWhere('codigo', 'ilike', "%{$search}%");
            });
        }

        // Scope
        if ($request->get('ativas') === 'true') {
            $query->ativas();
        }

        $perPage = $request->get('per_page', 15);
        $equipas = $query->orderBy('nome')->paginate($perPage);

        return response()->json($equipas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'modalidade_id' => 'required|exists:modalidades,id',
            'escalao_id' => 'required|exists:escaloes,id',
            'nome' => 'required|string|max:255',
            'codigo' => 'nullable|string|max:50|unique:equipas,codigo',
            'genero' => 'required|in:masculino,feminino,misto',
            'temporada' => 'required|string|max:20',
            'treinador_principal_id' => 'nullable|exists:membros,id',
            'local_treino' => 'nullable|string|max:255',
            'horario_treino' => 'nullable|string',
            'estado' => 'required|in:ativa,inativa,suspensa',
            'observacoes' => 'nullable|string',
        ]);

        $equipa = Equipa::create($validated);

        return response()->json([
            'message' => 'Equipa criada com sucesso',
            'data' => $equipa->load(['modalidade', 'escalao', 'treinadorPrincipal.pessoa'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $equipa = Equipa::with([
            'modalidade',
            'escalao',
            'treinadorPrincipal.pessoa',
            'atletas.membro.pessoa',
            'treinos',
            'competicoesCasa',
            'dadosDesportivos'
        ])->findOrFail($id);

        return response()->json($equipa);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $equipa = Equipa::findOrFail($id);

        $validated = $request->validate([
            'modalidade_id' => 'required|exists:modalidades,id',
            'escalao_id' => 'required|exists:escaloes,id',
            'nome' => 'required|string|max:255',
            'codigo' => 'nullable|string|max:50|unique:equipas,codigo,' . $id,
            'genero' => 'required|in:masculino,feminino,misto',
            'temporada' => 'required|string|max:20',
            'treinador_principal_id' => 'nullable|exists:membros,id',
            'local_treino' => 'nullable|string|max:255',
            'horario_treino' => 'nullable|string',
            'estado' => 'required|in:ativa,inativa,suspensa',
            'observacoes' => 'nullable|string',
        ]);

        $equipa->update($validated);

        return response()->json([
            'message' => 'Equipa atualizada com sucesso',
            'data' => $equipa->load(['modalidade', 'escalao', 'treinadorPrincipal.pessoa'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $equipa = Equipa::findOrFail($id);
        $equipa->delete();

        return response()->json([
            'message' => 'Equipa removida com sucesso'
        ]);
    }

    /**
     * Get team roster.
     */
    public function plantel(string $id)
    {
        $equipa = Equipa::with([
            'atletas' => function($query) {
                $query->wherePivot('ativo', true);
            },
            'atletas.membro.pessoa'
        ])->findOrFail($id);

        return response()->json([
            'equipa' => $equipa->only(['id', 'nome', 'temporada']),
            'atletas' => $equipa->atletas
        ]);
    }

    /**
     * Add athletes to team.
     */
    public function adicionarAtletas(Request $request, string $id)
    {
        $equipa = Equipa::findOrFail($id);

        $validated = $request->validate([
            'atletas' => 'required|array',
            'atletas.*.atleta_id' => 'required|exists:atletas,id',
            'atletas.*.numero_camisola' => 'nullable|integer',
            'atletas.*.posicao' => 'nullable|string|max:50',
            'atletas.*.titular' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['atletas'] as $atletaData) {
                $equipa->atletas()->attach($atletaData['atleta_id'], [
                    'data_inicio' => now(),
                    'numero_camisola' => $atletaData['numero_camisola'] ?? null,
                    'posicao' => $atletaData['posicao'] ?? null,
                    'titular' => $atletaData['titular'] ?? false,
                    'ativo' => true,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Atletas adicionados com sucesso',
                'data' => $equipa->load('atletas.membro.pessoa')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao adicionar atletas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
