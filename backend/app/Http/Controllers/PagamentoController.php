<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use App\Models\Fatura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PagamentoController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fatura_id' => 'required|exists:faturas,id',
            'metodo_pagamento_id' => 'required|exists:metodos_pagamento,id',
            'data_pagamento' => 'required|date',
            'valor' => 'required|numeric|min:0',
            'referencia' => 'nullable|string|max:255',
            'comprovativo' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $fatura = Fatura::findOrFail($validated['fatura_id']);

            // Verificar se o valor não excede o pendente
            if ($validated['valor'] > $fatura->valor_pendente) {
                return response()->json([
                    'message' => 'Valor do pagamento excede o valor pendente da fatura'
                ], 422);
            }

            // Gerar número de pagamento
            $ultimoPagamento = Pagamento::whereYear('data_pagamento', date('Y'))->max('numero_pagamento');
            $numero = $ultimoPagamento ? (intval(substr($ultimoPagamento, -6)) + 1) : 1;
            $numeroPagamento = 'PAG' . date('Y') . '/' . str_pad($numero, 6, '0', STR_PAD_LEFT);

            $pagamento = Pagamento::create([
                'fatura_id' => $validated['fatura_id'],
                'metodo_pagamento_id' => $validated['metodo_pagamento_id'],
                'numero_pagamento' => $numeroPagamento,
                'data_pagamento' => $validated['data_pagamento'],
                'valor' => $validated['valor'],
                'referencia' => $validated['referencia'] ?? null,
                'comprovativo' => $validated['comprovativo'] ?? null,
                'estado' => 'pendente',
                'observacoes' => $validated['observacoes'] ?? null,
                'registado_por' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Pagamento registado com sucesso',
                'data' => $pagamento->load(['fatura', 'metodoPagamento'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao registar pagamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function confirmar(string $id)
    {
        $pagamento = Pagamento::findOrFail($id);

        if ($pagamento->estado === 'confirmado') {
            return response()->json([
                'message' => 'Pagamento já está confirmado'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $pagamento->update([
                'estado' => 'confirmado',
                'confirmado_por' => Auth::id(),
                'data_confirmacao' => now(),
            ]);

            // Atualizar valores da fatura
            $fatura = $pagamento->fatura;
            $fatura->valor_pago += $pagamento->valor;
            $fatura->valor_pendente -= $pagamento->valor;

            // Atualizar estado da fatura
            if ($fatura->valor_pendente <= 0) {
                $fatura->estado = 'paga';
            } elseif ($fatura->valor_pago > 0) {
                $fatura->estado = 'parcial';
            }

            $fatura->save();

            DB::commit();

            return response()->json([
                'message' => 'Pagamento confirmado com sucesso',
                'data' => $pagamento->load('fatura')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao confirmar pagamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function cancelar(Request $request, string $id)
    {
        $pagamento = Pagamento::findOrFail($id);

        $validated = $request->validate([
            'observacoes' => 'required|string',
        ]);

        if ($pagamento->estado === 'cancelado') {
            return response()->json([
                'message' => 'Pagamento já está cancelado'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Se já estava confirmado, ajustar valores da fatura
            if ($pagamento->estado === 'confirmado') {
                $fatura = $pagamento->fatura;
                $fatura->valor_pago -= $pagamento->valor;
                $fatura->valor_pendente += $pagamento->valor;

                if ($fatura->valor_pago <= 0) {
                    $fatura->estado = 'pendente';
                } else {
                    $fatura->estado = 'parcial';
                }

                $fatura->save();
            }

            $pagamento->update([
                'estado' => 'cancelado',
                'observacoes' => $validated['observacoes'],
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Pagamento cancelado com sucesso',
                'data' => $pagamento
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao cancelar pagamento',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
