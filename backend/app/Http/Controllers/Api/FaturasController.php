<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaturaResource;
use App\Models\Fatura;
use App\Services\Financeiro\FaturacaoService;
use App\Services\Financeiro\ContaCorrenteService;
use App\Services\Tenancy\ClubContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Controller para gestão de faturas (Nova versão v2)
 */
class FaturasController extends Controller
{
    protected FaturacaoService $faturacaoService;
    protected ContaCorrenteService $contaCorrenteService;
    protected ClubContext $clubContext;

    public function __construct(
        FaturacaoService $faturacaoService,
        ContaCorrenteService $contaCorrenteService,
        ClubContext $clubContext
    ) {
        $this->faturacaoService = $faturacaoService;
        $this->contaCorrenteService = $contaCorrenteService;
        $this->clubContext = $clubContext;
    }

    /**
     * Listar faturas do clube
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Fatura::class);

        // ClubScope já filtra automaticamente por club_id
        $query = Fatura::with(['membro.user', 'itens', 'pagamentos']);

        // Filtros
        if ($request->has('membro_id')) {
            $query->where('membro_id', $request->membro_id);
        }

        if ($request->has('mes')) {
            $query->where('mes', $request->mes);
        }

        if ($request->has('estado')) {
            $query->where('status_cache', $request->estado);
        }

        $faturas = $query->orderBy('data_emissao', 'desc')
            ->paginate($request->per_page ?? 15);

        return FaturaResource::collection($faturas);
    }

    /**
     * Gerar faturas de mensalidade
     */
    public function gerarMensalidades(Request $request): JsonResponse
    {
        $this->authorize('generateMensalidades', Fatura::class);

        $request->validate([
            'membro_id' => 'required|exists:membros,id',
            'mes_inicio' => 'required|date_format:Y-m',
            'mes_fim' => 'nullable|date_format:Y-m',
        ]);

        try {
            $faturas = $this->faturacaoService->gerarFaturasMensalidade(
                $request->membro_id,
                $request->mes_inicio,
                $request->mes_fim
            );

            return response()->json([
                'message' => count($faturas) . ' faturas geradas com sucesso',
                'data' => FaturaResource::collection($faturas),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao gerar faturas',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Criar fatura avulsa
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Fatura::class);

        $request->validate([
            'membro_id' => 'required|exists:membros,id',
            'itens' => 'required|array|min:1',
            'itens.*.descricao' => 'required|string',
            'itens.*.valor_unitario' => 'required|numeric|min:0',
            'itens.*.quantidade' => 'required|integer|min:1',
        ]);

        try {
            $fatura = $this->faturacaoService->criarFaturaAvulsa(
                $request->membro_id,
                $request->itens,
                $request->only(['data_emissao', 'mes', 'centro_custo_id'])
            );

            return (new FaturaResource($fatura->load(['itens', 'membro.user', 'pagamentos'])))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao criar fatura',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Obter detalhes de fatura
     */
    public function show(int $id): FaturaResource
    {
        // ClubScope já filtra automaticamente por club_id
        $fatura = Fatura::with(['membro.user', 'itens', 'pagamentos', 'centroCusto'])
            ->findOrFail($id);

        $this->authorize('view', $fatura);

        return new FaturaResource($fatura);
    }

    /**
     * Adicionar item a fatura
     */
    public function adicionarItem(Request $request, int $id): JsonResponse
    {
        // ClubScope já filtra automaticamente
        $fatura = Fatura::findOrFail($id);
        $this->authorize('update', $fatura);

        $request->validate([
            'descricao' => 'required|string',
            'valor_unitario' => 'required|numeric|min:0',
            'quantidade' => 'required|integer|min:1',
        ]);

        try {
            $item = $this->faturacaoService->adicionarItemFatura($id, $request->all());

            return response()->json([
                'message' => 'Item adicionado com sucesso',
                'item' => $item,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao adicionar item',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Registar pagamento
     */
    public function registarPagamento(Request $request, int $id): JsonResponse
    {
        // ClubScope já filtra automaticamente
        $fatura = Fatura::findOrFail($id);
        $this->authorize('update', $fatura);

        $request->validate([
            'valor' => 'required|numeric|min:0.01',
            'metodo' => 'required|string|in:dinheiro,transferencia,multibanco,mbway,cartao',
            'data_pagamento' => 'nullable|date',
        ]);

        try {
            $pagamento = $this->contaCorrenteService->registarPagamento($id, $request->all());

            return response()->json([
                'message' => 'Pagamento registado com sucesso',
                'pagamento' => $pagamento,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao registar pagamento',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Obter conta corrente de um membro
     */
    public function contaCorrente(int $membroId): JsonResponse
    {
        $this->authorize('viewAny', Fatura::class);

        try {
            $conta = $this->contaCorrenteService->contaCorrente($membroId);

            return response()->json($conta);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao obter conta corrente',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Obter resumo financeiro de um membro
     */
    public function resumoFinanceiro(int $membroId): JsonResponse
    {
        try {
            $resumo = $this->contaCorrenteService->resumoFinanceiro($membroId);

            return response()->json($resumo);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao obter resumo financeiro',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
