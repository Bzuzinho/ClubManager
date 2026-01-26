<?php

namespace App\Http\Controllers;

use App\Models\Competicao;
use App\Models\Convocatoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CompeticaoController extends Controller
{
    public function index(Request $request)
    {
        $query = Competicao::with(['modalidade', 'equipaCasa']);

        if ($request->has('modalidade_id')) {
            $query->where('modalidade_id', $request->modalidade_id);
        }

        if ($request->has('equipa_casa_id')) {
            $query->where('equipa_casa_id', $request->equipa_casa_id);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->get('agendadas') === 'true') {
            $query->agendadas();
        } elseif ($request->get('finalizadas') === 'true') {
            $query->finalizadas();
        }

        $perPage = $request->get('per_page', 15);
        $competicoes = $query->orderBy('data', 'desc')->paginate($perPage);

        return response()->json($competicoes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'modalidade_id' => 'required|exists:modalidades,id',
            'equipa_casa_id' => 'required|exists:equipas,id',
            'adversario' => 'required|string|max:255',
            'tipo' => 'required|in:campeonato,taca,amigavel,torneio',
            'data' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'local' => 'required|string|max:255',
            'casa' => 'required|boolean',
            'competicao' => 'nullable|string|max:255',
            'jornada' => 'nullable|string|max:50',
            'observacoes' => 'nullable|string',
        ]);

        $validated['estado'] = 'agendado';

        $competicao = Competicao::create($validated);

        return response()->json([
            'message' => 'Competição criada com sucesso',
            'data' => $competicao->load(['modalidade', 'equipaCasa'])
        ], 201);
    }

    public function show(string $id)
    {
        $competicao = Competicao::with([
            'modalidade',
            'equipaCasa.atletas.membro.pessoa',
            'convocatorias.atleta.membro.pessoa'
        ])->findOrFail($id);

        return response()->json($competicao);
    }

    public function update(Request $request, string $id)
    {
        $competicao = Competicao::findOrFail($id);

        $validated = $request->validate([
            'modalidade_id' => 'required|exists:modalidades,id',
            'equipa_casa_id' => 'required|exists:equipas,id',
            'adversario' => 'required|string|max:255',
            'tipo' => 'required|in:campeonato,taca,amigavel,torneio',
            'data' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'local' => 'required|string|max:255',
            'casa' => 'required|boolean',
            'competicao' => 'nullable|string|max:255',
            'jornada' => 'nullable|string|max:50',
            'estado' => 'required|in:agendado,realizado,cancelado,adiado',
            'golos_favor' => 'nullable|integer|min:0',
            'golos_contra' => 'nullable|integer|min:0',
            'resultado' => 'nullable|in:vitoria,derrota,empate',
            'relatorio' => 'nullable|string',
            'observacoes' => 'nullable|string',
        ]);

        $competicao->update($validated);

        return response()->json([
            'message' => 'Competição atualizada com sucesso',
            'data' => $competicao
        ]);
    }

    public function destroy(string $id)
    {
        $competicao = Competicao::findOrFail($id);
        $competicao->delete();

        return response()->json([
            'message' => 'Competição removida com sucesso'
        ]);
    }

    public function convocar(Request $request, string $id)
    {
        $competicao = Competicao::findOrFail($id);

        $validated = $request->validate([
            'atletas' => 'required|array|min:1',
            'atletas.*.atleta_id' => 'required|exists:atletas,id',
            'atletas.*.titular' => 'boolean',
            'atletas.*.observacoes' => 'nullable|string',
            'hora_concentracao' => 'nullable|date_format:H:i',
            'local_concentracao' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['atletas'] as $atletaData) {
                Convocatoria::create([
                    'competicao_id' => $competicao->id,
                    'atleta_id' => $atletaData['atleta_id'],
                    'estado' => 'convocado',
                    'titular' => $atletaData['titular'] ?? false,
                    'hora_concentracao' => $validated['hora_concentracao'] ?? null,
                    'local_concentracao' => $validated['local_concentracao'] ?? null,
                    'observacoes' => $atletaData['observacoes'] ?? null,
                    'convocado_por' => Auth::id(),
                    'data_convocatoria' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Convocatórias criadas com sucesso',
                'data' => $competicao->load('convocatorias.atleta.membro.pessoa')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao criar convocatórias',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
