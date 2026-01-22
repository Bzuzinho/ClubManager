<?php

namespace App\Http\Controllers;

use App\Models\Membro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MembroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Membro::with(['pessoa', 'tipos', 'atleta']);

        // Filtros
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('pessoa', function($q) use ($search) {
                $q->where('nome_completo', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            })->orWhere('numero_socio', 'like', "%{$search}%");
        }

        if ($request->has('tipo_membro_id')) {
            $query->whereHas('tipos', function($q) use ($request) {
                $q->where('tipo_membro_id', $request->tipo_membro_id);
            });
        }

        // Scopes
        if ($request->get('ativos') === 'true') {
            $query->ativos();
        } elseif ($request->get('inativos') === 'true') {
            $query->inativos();
        } elseif ($request->get('pendentes') === 'true') {
            $query->pendentes();
        }

        $perPage = $request->get('per_page', 15);
        $membros = $query->orderBy('numero_socio')->paginate($perPage);

        return response()->json($membros);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pessoa_id' => 'required|exists:pessoas,id',
            'numero_socio' => 'nullable|string|max:50|unique:membros,numero_socio',
            'estado' => 'required|in:ativo,inativo,pendente,suspenso',
            'data_inscricao' => 'nullable|date',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date',
            'motivo_inativacao' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'tipos' => 'required|array',
            'tipos.*.tipo_membro_id' => 'required|exists:tipos_membro,id',
            'tipos.*.data_inicio' => 'nullable|date',
            'tipos.*.ativo' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Gerar número de sócio se não fornecido
            if (empty($validated['numero_socio'])) {
                $ultimoNumero = Membro::max('numero_socio') ?? 0;
                $validated['numero_socio'] = str_pad($ultimoNumero + 1, 6, '0', STR_PAD_LEFT);
            }

            $tipos = $validated['tipos'];
            unset($validated['tipos']);

            $membro = Membro::create($validated);

            // Associar tipos de membro
            foreach ($tipos as $tipo) {
                $membro->tipos()->attach($tipo['tipo_membro_id'], [
                    'data_inicio' => $tipo['data_inicio'] ?? now(),
                    'ativo' => $tipo['ativo'] ?? true,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Membro criado com sucesso',
                'data' => $membro->load(['pessoa', 'tipos'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao criar membro',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $membro = Membro::with([
            'pessoa.user',
            'tipos',
            'atleta.equipas.modalidade',
            'faturas.itens',
            'inscricoesEvento.evento',
            'equipasTreinador',
            'documentos.tipoDocumento'
        ])->findOrFail($id);

        return response()->json($membro);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $membro = Membro::findOrFail($id);

        $validated = $request->validate([
            'pessoa_id' => 'required|exists:pessoas,id',
            'numero_socio' => 'nullable|string|max:50|unique:membros,numero_socio,' . $id,
            'estado' => 'required|in:ativo,inativo,pendente,suspenso',
            'data_inscricao' => 'nullable|date',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date',
            'motivo_inativacao' => 'nullable|string',
            'observacoes' => 'nullable|string',
        ]);

        $membro->update($validated);

        return response()->json([
            'message' => 'Membro atualizado com sucesso',
            'data' => $membro->load(['pessoa', 'tipos'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $membro = Membro::findOrFail($id);
        $membro->delete();

        return response()->json([
            'message' => 'Membro removido com sucesso'
        ]);
    }

    /**
     * Update member types.
     */
    public function updateTipos(Request $request, string $id)
    {
        $membro = Membro::findOrFail($id);

        $validated = $request->validate([
            'tipos' => 'required|array',
            'tipos.*.tipo_membro_id' => 'required|exists:tipos_membro,id',
            'tipos.*.data_inicio' => 'nullable|date',
            'tipos.*.data_fim' => 'nullable|date',
            'tipos.*.ativo' => 'boolean',
            'tipos.*.observacoes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Remover tipos anteriores
            $membro->tipos()->detach();

            // Adicionar novos tipos
            foreach ($validated['tipos'] as $tipo) {
                $membro->tipos()->attach($tipo['tipo_membro_id'], [
                    'data_inicio' => $tipo['data_inicio'] ?? now(),
                    'data_fim' => $tipo['data_fim'] ?? null,
                    'ativo' => $tipo['ativo'] ?? true,
                    'observacoes' => $tipo['observacoes'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Tipos de membro atualizados com sucesso',
                'data' => $membro->load('tipos')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao atualizar tipos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
