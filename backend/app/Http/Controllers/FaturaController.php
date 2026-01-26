<?php

namespace App\Http\Controllers;

use App\Models\Fatura;
use App\Models\ItemFatura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FaturaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Fatura::with(['membro.pessoa', 'emitidaPor']);

        // Filtros
        if ($request->has('membro_id')) {
            $query->where('membro_id', $request->membro_id);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->has('data_inicio') && $request->has('data_fim')) {
            $query->whereBetween('data_emissao', [$request->data_inicio, $request->data_fim]);
        }

        // Scopes
        if ($request->get('pendentes') === 'true') {
            $query->pendentes();
        } elseif ($request->get('pagas') === 'true') {
            $query->pagas();
        } elseif ($request->get('vencidas') === 'true') {
            $query->vencidas();
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_fatura', 'like', "%{$search}%")
                  ->orWhere('referencia_mb', 'like', "%{$search}%")
                  ->orWhereHas('membro.pessoa', function($q2) use ($search) {
                      $q2->where('nome_completo', 'ilike', "%{$search}%");
                  });
            });
        }

        $perPage = $request->get('per_page', 15);
        $faturas = $query->orderBy('data_emissao', 'desc')->paginate($perPage);

        return response()->json($faturas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'membro_id' => 'required|exists:membros,id',
            'data_emissao' => 'required|date',
            'data_vencimento' => 'required|date|after_or_equal:data_emissao',
            'tipo' => 'required|in:mensalidade,inscricao,evento,outros',
            'observacoes' => 'nullable|string',
            'itens' => 'required|array|min:1',
            'itens.*.descricao' => 'required|string|max:255',
            'itens.*.quantidade' => 'required|integer|min:1',
            'itens.*.preco_unitario' => 'required|numeric|min:0',
            'itens.*.desconto' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Gerar número de fatura
            $ultimaFatura = Fatura::whereYear('data_emissao', date('Y'))->max('numero_fatura');
            $numero = $ultimaFatura ? (intval(substr($ultimaFatura, -6)) + 1) : 1;
            $numeroFatura = date('Y') . '/' . str_pad($numero, 6, '0', STR_PAD_LEFT);

            // Calcular totais
            $valorTotal = 0;
            foreach ($validated['itens'] as $item) {
                $subtotal = $item['quantidade'] * $item['preco_unitario'];
                $desconto = $item['desconto'] ?? 0;
                $valorTotal += ($subtotal - $desconto);
            }

            // Criar fatura
            $fatura = Fatura::create([
                'membro_id' => $validated['membro_id'],
                'numero_fatura' => $numeroFatura,
                'data_emissao' => $validated['data_emissao'],
                'data_vencimento' => $validated['data_vencimento'],
                'valor_total' => $valorTotal,
                'valor_pago' => 0,
                'valor_pendente' => $valorTotal,
                'estado' => 'pendente',
                'tipo' => $validated['tipo'],
                'observacoes' => $validated['observacoes'] ?? null,
                'emitida_por' => Auth::id(),
            ]);

            // Criar itens
            foreach ($validated['itens'] as $itemData) {
                $subtotal = $itemData['quantidade'] * $itemData['preco_unitario'];
                $desconto = $itemData['desconto'] ?? 0;
                $total = $subtotal - $desconto;

                ItemFatura::create([
                    'fatura_id' => $fatura->id,
                    'descricao' => $itemData['descricao'],
                    'quantidade' => $itemData['quantidade'],
                    'preco_unitario' => $itemData['preco_unitario'],
                    'subtotal' => $subtotal,
                    'desconto' => $desconto,
                    'total' => $total,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Fatura criada com sucesso',
                'data' => $fatura->load(['itens', 'membro.pessoa'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao criar fatura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $fatura = Fatura::with([
            'membro.pessoa',
            'itens',
            'pagamentos.metodoPagamento',
            'emitidaPor'
        ])->findOrFail($id);

        return response()->json($fatura);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $fatura = Fatura::findOrFail($id);

        // Apenas permitir atualização se ainda não foi paga
        if ($fatura->estado === 'paga') {
            return response()->json([
                'message' => 'Não é possível atualizar uma fatura já paga'
            ], 422);
        }

        $validated = $request->validate([
            'data_vencimento' => 'required|date',
            'observacoes' => 'nullable|string',
        ]);

        $fatura->update($validated);

        return response()->json([
            'message' => 'Fatura atualizada com sucesso',
            'data' => $fatura
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $fatura = Fatura::findOrFail($id);

        // Apenas permitir remoção se não tiver pagamentos
        if ($fatura->pagamentos()->exists()) {
            return response()->json([
                'message' => 'Não é possível remover uma fatura com pagamentos registados'
            ], 422);
        }

        $fatura->delete();

        return response()->json([
            'message' => 'Fatura removida com sucesso'
        ]);
    }

    /**
     * Cancel an invoice.
     */
    public function cancelar(string $id)
    {
        $fatura = Fatura::findOrFail($id);

        if ($fatura->estado === 'cancelada') {
            return response()->json([
                'message' => 'Fatura já está cancelada'
            ], 422);
        }

        if ($fatura->pagamentos()->where('estado', 'confirmado')->exists()) {
            return response()->json([
                'message' => 'Não é possível cancelar uma fatura com pagamentos confirmados'
            ], 422);
        }

        $fatura->update(['estado' => 'cancelada']);

        return response()->json([
            'message' => 'Fatura cancelada com sucesso',
            'data' => $fatura
        ]);
    }
}
