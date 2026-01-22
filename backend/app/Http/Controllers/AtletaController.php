<?php

namespace App\Http\Controllers;

use App\Models\Atleta;
use App\Models\Membro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AtletaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Atleta::with(['membro.pessoa', 'equipas.modalidade', 'equipas.escalao']);

        // Filtros
        if ($request->has('ativo')) {
            $query->where('ativo', $request->boolean('ativo'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('membro.pessoa', function($q) use ($search) {
                $q->where('nome_completo', 'ilike', "%{$search}%");
            })->orWhere('numero_camisola', 'like', "%{$search}%");
        }

        if ($request->has('equipa_id')) {
            $query->whereHas('equipas', function($q) use ($request) {
                $q->where('equipa_id', $request->equipa_id);
            });
        }

        if ($request->has('posicao')) {
            $query->where('posicao_preferida', $request->posicao);
        }

        // Scope
        if ($request->get('ativos_only') === 'true') {
            $query->ativos();
        }

        $perPage = $request->get('per_page', 15);
        $atletas = $query->paginate($perPage);

        return response()->json($atletas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'membro_id' => 'required|exists:membros,id',
            'ativo' => 'boolean',
            'numero_camisola' => 'nullable|integer',
            'tamanho_equipamento' => 'nullable|string|max:10',
            'altura' => 'nullable|numeric|min:0',
            'peso' => 'nullable|numeric|min:0',
            'pe_dominante' => 'nullable|in:direito,esquerdo,ambidestro',
            'posicao_preferida' => 'nullable|string|max:50',
            'observacoes_medicas' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'equipas' => 'nullable|array',
            'equipas.*.equipa_id' => 'required|exists:equipas,id',
            'equipas.*.numero_camisola' => 'nullable|integer',
            'equipas.*.posicao' => 'nullable|string|max:50',
            'equipas.*.titular' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $equipas = $validated['equipas'] ?? [];
            unset($validated['equipas']);

            $atleta = Atleta::create($validated);

            // Associar equipas
            foreach ($equipas as $equipa) {
                $atleta->equipas()->attach($equipa['equipa_id'], [
                    'data_inicio' => now(),
                    'numero_camisola' => $equipa['numero_camisola'] ?? null,
                    'posicao' => $equipa['posicao'] ?? null,
                    'titular' => $equipa['titular'] ?? false,
                    'ativo' => true,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Atleta criado com sucesso',
                'data' => $atleta->load(['membro.pessoa', 'equipas'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao criar atleta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $atleta = Atleta::with([
            'membro.pessoa',
            'equipas.modalidade',
            'equipas.escalao',
            'encarregados.pessoa',
            'presencasTreino.treino',
            'convocatorias.competicao',
            'dadosDesportivos.equipa',
            'documentos.tipoDocumento'
        ])->findOrFail($id);

        return response()->json($atleta);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $atleta = Atleta::findOrFail($id);

        $validated = $request->validate([
            'membro_id' => 'required|exists:membros,id',
            'ativo' => 'boolean',
            'numero_camisola' => 'nullable|integer',
            'tamanho_equipamento' => 'nullable|string|max:10',
            'altura' => 'nullable|numeric|min:0',
            'peso' => 'nullable|numeric|min:0',
            'pe_dominante' => 'nullable|in:direito,esquerdo,ambidestro',
            'posicao_preferida' => 'nullable|string|max:50',
            'observacoes_medicas' => 'nullable|string',
            'observacoes' => 'nullable|string',
        ]);

        $atleta->update($validated);

        return response()->json([
            'message' => 'Atleta atualizado com sucesso',
            'data' => $atleta->load(['membro.pessoa', 'equipas'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $atleta = Atleta::findOrFail($id);
        $atleta->delete();

        return response()->json([
            'message' => 'Atleta removido com sucesso'
        ]);
    }

    /**
     * Update athlete teams.
     */
    public function updateEquipas(Request $request, string $id)
    {
        $atleta = Atleta::findOrFail($id);

        $validated = $request->validate([
            'equipas' => 'required|array',
            'equipas.*.equipa_id' => 'required|exists:equipas,id',
            'equipas.*.data_inicio' => 'nullable|date',
            'equipas.*.numero_camisola' => 'nullable|integer',
            'equipas.*.posicao' => 'nullable|string|max:50',
            'equipas.*.titular' => 'boolean',
            'equipas.*.capitao' => 'boolean',
            'equipas.*.ativo' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $atleta->equipas()->detach();

            foreach ($validated['equipas'] as $equipa) {
                $atleta->equipas()->attach($equipa['equipa_id'], [
                    'data_inicio' => $equipa['data_inicio'] ?? now(),
                    'numero_camisola' => $equipa['numero_camisola'] ?? null,
                    'posicao' => $equipa['posicao'] ?? null,
                    'titular' => $equipa['titular'] ?? false,
                    'capitao' => $equipa['capitao'] ?? false,
                    'ativo' => $equipa['ativo'] ?? true,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Equipas atualizadas com sucesso',
                'data' => $atleta->load('equipas')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao atualizar equipas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get athlete statistics.
     */
    public function estatisticas(string $id, Request $request)
    {
        $atleta = Atleta::findOrFail($id);
        
        $temporada = $request->get('temporada');
        $equipaId = $request->get('equipa_id');

        $query = $atleta->dadosDesportivos();

        if ($temporada) {
            $query->where('temporada', $temporada);
        }

        if ($equipaId) {
            $query->where('equipa_id', $equipaId);
        }

        $dados = $query->with('equipa')->get();

        return response()->json([
            'atleta' => $atleta->load('membro.pessoa'),
            'estatisticas' => $dados
        ]);
    }
}
