<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use App\Http\Requests\StorePessoaRequest;
use App\Http\Requests\UpdatePessoaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PessoaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pessoa::with(['user', 'membro']);

        // Filtros
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome_completo', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('nif', 'like', "%{$search}%")
                  ->orWhere('telemovel', 'like', "%{$search}%");
            });
        }

        if ($request->has('nacionalidade')) {
            $query->where('nacionalidade', $request->nacionalidade);
        }

        $perPage = $request->get('per_page', 15);
        $pessoas = $query->orderBy('nome_completo')->paginate($perPage);

        return response()->json($pessoas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePessoaRequest $request)
    {
        $pessoa = Pessoa::create($request->validated());

        return response()->json([
            'message' => 'Pessoa criada com sucesso',
            'data' => $pessoa->load(['user', 'membro'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pessoa = Pessoa::with([
            'user',
            'membro.tipos',
            'membro.atleta',
            'encarregadoEducacao.atletas',
            'relacoesOrigem.pessoaDestino',
            'relacoesDestino.pessoaOrigem',
            'consentimentos',
            'documentos.tipoDocumento'
        ])->findOrFail($id);

        return response()->json($pessoa);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePessoaRequest $request, string $id)
    {
        $pessoa = Pessoa::findOrFail($id);

        $pessoa->update($request->validated());

        return response()->json([
            'message' => 'Pessoa atualizada com sucesso',
            'data' => $pessoa->load(['user', 'membro'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pessoa = Pessoa::findOrFail($id);
        $pessoa->delete();

        return response()->json([
            'message' => 'Pessoa removida com sucesso'
        ]);
    }

    /**
     * Restore a soft deleted resource.
     */
    public function restore(string $id)
    {
        $pessoa = Pessoa::withTrashed()->findOrFail($id);
        $pessoa->restore();

        return response()->json([
            'message' => 'Pessoa restaurada com sucesso',
            'data' => $pessoa
        ]);
    }
}
