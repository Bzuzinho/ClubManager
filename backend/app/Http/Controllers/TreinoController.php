<?php

namespace App\Http\Controllers;

use App\Models\Treino;
use App\Models\PresencaTreino;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TreinoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Treino::with(['equipa.modalidade', 'responsavel.pessoa']);

        // Filtros
        if ($request->has('equipa_id')) {
            $query->where('equipa_id', $request->equipa_id);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('data_inicio') && $request->has('data_fim')) {
            $query->whereBetween('data', [$request->data_inicio, $request->data_fim]);
        }

        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Scopes
        if ($request->get('agendados') === 'true') {
            $query->agendados();
        } elseif ($request->get('realizados') === 'true') {
            $query->realizados();
        }

        $perPage = $request->get('per_page', 15);
        $treinos = $query->orderBy('data', 'desc')->orderBy('hora_inicio', 'desc')->paginate($perPage);

        return response()->json($treinos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipa_id' => 'required|exists:equipas,id',
            'data' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'required|date_format:H:i|after:hora_inicio',
            'local' => 'required|string|max:255',
            'tipo' => 'required|in:tecnico,tatico,fisico,jogo_treino,recuperacao',
            'objetivos' => 'nullable|string',
            'descricao' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'responsavel_id' => 'nullable|exists:membros,id',
            'estado' => 'required|in:agendado,realizado,cancelado',
        ]);

        $treino = Treino::create($validated);

        return response()->json([
            'message' => 'Treino criado com sucesso',
            'data' => $treino->load(['equipa', 'responsavel.pessoa'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $treino = Treino::with([
            'equipa.modalidade',
            'equipa.atletas.membro.pessoa',
            'responsavel.pessoa',
            'presencas.atleta.membro.pessoa'
        ])->findOrFail($id);

        return response()->json($treino);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $treino = Treino::findOrFail($id);

        $validated = $request->validate([
            'equipa_id' => 'required|exists:equipas,id',
            'data' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'required|date_format:H:i|after:hora_inicio',
            'local' => 'required|string|max:255',
            'tipo' => 'required|in:tecnico,tatico,fisico,jogo_treino,recuperacao',
            'objetivos' => 'nullable|string',
            'descricao' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'responsavel_id' => 'nullable|exists:membros,id',
            'estado' => 'required|in:agendado,realizado,cancelado',
            'motivo_cancelamento' => 'nullable|string',
        ]);

        $treino->update($validated);

        return response()->json([
            'message' => 'Treino atualizado com sucesso',
            'data' => $treino->load(['equipa', 'responsavel.pessoa'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $treino = Treino::findOrFail($id);
        $treino->delete();

        return response()->json([
            'message' => 'Treino removido com sucesso'
        ]);
    }

    /**
     * Register attendances for a training session.
     */
    public function registarPresencas(Request $request, string $id)
    {
        $treino = Treino::findOrFail($id);

        $validated = $request->validate([
            'presencas' => 'required|array',
            'presencas.*.atleta_id' => 'required|exists:atletas,id',
            'presencas.*.estado' => 'required|in:presente,ausente,justificado,atrasado',
            'presencas.*.hora_chegada' => 'nullable|date_format:H:i',
            'presencas.*.hora_saida' => 'nullable|date_format:H:i',
            'presencas.*.justificacao' => 'nullable|string',
            'presencas.*.observacoes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['presencas'] as $presencaData) {
                PresencaTreino::updateOrCreate(
                    [
                        'treino_id' => $treino->id,
                        'atleta_id' => $presencaData['atleta_id'],
                    ],
                    [
                        'estado' => $presencaData['estado'],
                        'hora_chegada' => $presencaData['hora_chegada'] ?? null,
                        'hora_saida' => $presencaData['hora_saida'] ?? null,
                        'justificacao' => $presencaData['justificacao'] ?? null,
                        'observacoes' => $presencaData['observacoes'] ?? null,
                        'registado_por' => Auth::id(),
                    ]
                );
            }

            // Atualizar estado do treino para realizado
            $treino->update(['estado' => 'realizado']);

            DB::commit();

            return response()->json([
                'message' => 'Presenças registadas com sucesso',
                'data' => $treino->load('presencas.atleta.membro.pessoa')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao registar presenças',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance statistics for a training session.
     */
    public function estatisticasPresenca(string $id)
    {
        $treino = Treino::with('presencas')->findOrFail($id);

        $total = $treino->presencas->count();
        $presentes = $treino->presencas->where('estado', 'presente')->count();
        $ausentes = $treino->presencas->where('estado', 'ausente')->count();
        $justificados = $treino->presencas->where('estado', 'justificado')->count();
        $atrasados = $treino->presencas->where('estado', 'atrasado')->count();

        return response()->json([
            'treino' => $treino->only(['id', 'data', 'hora_inicio', 'local']),
            'estatisticas' => [
                'total' => $total,
                'presentes' => $presentes,
                'ausentes' => $ausentes,
                'justificados' => $justificados,
                'atrasados' => $atrasados,
                'percentagem_presenca' => $total > 0 ? round(($presentes / $total) * 100, 2) : 0,
            ]
        ]);
    }
}
