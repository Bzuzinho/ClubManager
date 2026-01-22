<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'nome_completo' => 'required|string|max:255',
            'nif' => 'nullable|string|max:20|unique:pessoas,nif',
            'email' => 'nullable|email|max:255',
            'telemovel' => 'nullable|string|max:20',
            'telefone_fixo' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'nacionalidade' => 'nullable|string|max:100',
            'naturalidade' => 'nullable|string|max:100',
            'numero_identificacao' => 'nullable|string|max:50',
            'validade_identificacao' => 'nullable|date',
            'morada' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:20',
            'localidade' => 'nullable|string|max:100',
            'distrito' => 'nullable|string|max:100',
            'foto_perfil' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
        ]);

        $pessoa = Pessoa::create($validated);

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
    public function update(Request $request, string $id)
    {
        $pessoa = Pessoa::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'nome_completo' => 'required|string|max:255',
            'nif' => 'nullable|string|max:20|unique:pessoas,nif,' . $id,
            'email' => 'nullable|email|max:255',
            'telemovel' => 'nullable|string|max:20',
            'telefone_fixo' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'nacionalidade' => 'nullable|string|max:100',
            'naturalidade' => 'nullable|string|max:100',
            'numero_identificacao' => 'nullable|string|max:50',
            'validade_identificacao' => 'nullable|date',
            'morada' => 'nullable|string|max:255',
            'codigo_postal' => 'nullable|string|max:20',
            'localidade' => 'nullable|string|max:100',
            'distrito' => 'nullable|string|max:100',
            'foto_perfil' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
        ]);

        $pessoa->update($validated);

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
