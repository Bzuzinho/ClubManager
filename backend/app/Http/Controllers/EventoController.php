<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\InscricaoEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EventoController extends Controller
{
    public function index(Request $request)
    {
        $query = Evento::with(['tipoEvento', 'criadoPor']);

        if ($request->has('tipo_evento_id')) {
            $query->where('tipo_evento_id', $request->tipo_evento_id);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->get('publicos') === 'true') {
            $query->publicos();
        }

        if ($request->get('publicados') === 'true') {
            $query->publicados();
        }

        if ($request->has('data_inicio')) {
            $query->where('data_inicio', '>=', $request->data_inicio);
        }

        $perPage = $request->get('per_page', 15);
        $eventos = $query->orderBy('data_inicio', 'desc')->paginate($perPage);

        return response()->json($eventos);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_evento_id' => 'required|exists:tipos_evento,id',
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fim' => 'nullable|date_format:H:i',
            'local' => 'nullable|string|max:255',
            'morada_completa' => 'nullable|string',
            'preco' => 'nullable|numeric|min:0',
            'vagas_totais' => 'nullable|integer|min:0',
            'data_limite_inscricao' => 'nullable|date',
            'publico' => 'boolean',
            'requer_aprovacao' => 'boolean',
            'estado' => 'required|in:rascunho,publicado,cancelado,finalizado',
            'imagem' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
        ]);

        $validated['vagas_disponiveis'] = $validated['vagas_totais'] ?? 0;
        $validated['criado_por'] = Auth::id();

        $evento = Evento::create($validated);

        return response()->json([
            'message' => 'Evento criado com sucesso',
            'data' => $evento->load(['tipoEvento', 'criadoPor'])
        ], 201);
    }

    public function show(string $id)
    {
        $evento = Evento::with([
            'tipoEvento',
            'criadoPor',
            'inscricoes.membro.pessoa'
        ])->findOrFail($id);

        return response()->json($evento);
    }

    public function update(Request $request, string $id)
    {
        $evento = Evento::findOrFail($id);

        $validated = $request->validate([
            'tipo_evento_id' => 'required|exists:tipos_evento,id',
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fim' => 'nullable|date_format:H:i',
            'local' => 'nullable|string|max:255',
            'morada_completa' => 'nullable|string',
            'preco' => 'nullable|numeric|min:0',
            'vagas_totais' => 'nullable|integer|min:0',
            'data_limite_inscricao' => 'nullable|date',
            'publico' => 'boolean',
            'requer_aprovacao' => 'boolean',
            'estado' => 'required|in:rascunho,publicado,cancelado,finalizado',
            'imagem' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
        ]);

        $evento->update($validated);

        return response()->json([
            'message' => 'Evento atualizado com sucesso',
            'data' => $evento
        ]);
    }

    public function destroy(string $id)
    {
        $evento = Evento::findOrFail($id);
        
        if ($evento->inscricoes()->exists()) {
            return response()->json([
                'message' => 'Não é possível remover um evento com inscrições'
            ], 422);
        }

        $evento->delete();

        return response()->json([
            'message' => 'Evento removido com sucesso'
        ]);
    }

    public function inscrever(Request $request, string $id)
    {
        $evento = Evento::findOrFail($id);

        $validated = $request->validate([
            'membro_id' => 'required|exists:membros,id',
            'numero_acompanhantes' => 'nullable|integer|min:0',
            'observacoes' => 'nullable|string',
        ]);

        // Verificar vagas
        if ($evento->vagas_totais && $evento->vagas_disponiveis <= 0) {
            return response()->json([
                'message' => 'Não há vagas disponíveis para este evento'
            ], 422);
        }

        // Verificar data limite
        if ($evento->data_limite_inscricao && now() > $evento->data_limite_inscricao) {
            return response()->json([
                'message' => 'Prazo de inscrição expirado'
            ], 422);
        }

        // Verificar se já está inscrito
        $inscricaoExistente = InscricaoEvento::where('evento_id', $id)
            ->where('membro_id', $validated['membro_id'])
            ->first();

        if ($inscricaoExistente) {
            return response()->json([
                'message' => 'Membro já está inscrito neste evento'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $inscricao = InscricaoEvento::create([
                'evento_id' => $id,
                'membro_id' => $validated['membro_id'],
                'estado' => $evento->requer_aprovacao ? 'pendente' : 'confirmada',
                'data_inscricao' => now(),
                'pago' => false,
                'valor_pago' => 0,
                'numero_acompanhantes' => $validated['numero_acompanhantes'] ?? 0,
                'observacoes' => $validated['observacoes'] ?? null,
            ]);

            // Atualizar vagas disponíveis
            if ($evento->vagas_totais) {
                $evento->decrement('vagas_disponiveis');
            }

            DB::commit();

            return response()->json([
                'message' => 'Inscrição realizada com sucesso',
                'data' => $inscricao->load(['evento', 'membro.pessoa'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao realizar inscrição',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
