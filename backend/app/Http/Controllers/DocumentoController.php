<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DocumentoController extends Controller
{
    public function index(Request $request)
    {
        $query = Documento::with(['tipoDocumento', 'uploadedBy']);

        if ($request->has('documentavel_type') && $request->has('documentavel_id')) {
            $query->where('documentavel_type', $request->documentavel_type)
                  ->where('documentavel_id', $request->documentavel_id);
        }

        if ($request->has('tipo_documento_id')) {
            $query->where('tipo_documento_id', $request->tipo_documento_id);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->get('validos') === 'true') {
            $query->validos();
        } elseif ($request->get('expirados') === 'true') {
            $query->expirados();
        } elseif ($request->get('pendentes') === 'true') {
            $query->pendentes();
        }

        $perPage = $request->get('per_page', 15);
        $documentos = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($documentos);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'documentavel_type' => 'required|string',
            'documentavel_id' => 'required|integer',
            'tipo_documento_id' => 'required|exists:tipos_documento,id',
            'ficheiro' => 'required|file|max:10240', // 10MB
            'data_emissao' => 'nullable|date',
            'data_validade' => 'nullable|date',
            'observacoes' => 'nullable|string',
        ]);

        try {
            $file = $request->file('ficheiro');
            $nomeOriginal = $file->getClientOriginalName();
            $nomeFicheiro = time() . '_' . $nomeOriginal;
            $caminho = $file->store('documentos', 'public');

            $documento = Documento::create([
                'documentavel_type' => $validated['documentavel_type'],
                'documentavel_id' => $validated['documentavel_id'],
                'tipo_documento_id' => $validated['tipo_documento_id'],
                'nome_original' => $nomeOriginal,
                'nome_ficheiro' => $nomeFicheiro,
                'caminho' => $caminho,
                'mime_type' => $file->getMimeType(),
                'tamanho' => $file->getSize(),
                'data_emissao' => $validated['data_emissao'] ?? null,
                'data_validade' => $validated['data_validade'] ?? null,
                'data_upload' => now(),
                'estado' => 'valido',
                'observacoes' => $validated['observacoes'] ?? null,
                'uploaded_by' => Auth::id(),
            ]);

            return response()->json([
                'message' => 'Documento enviado com sucesso',
                'data' => $documento->load(['tipoDocumento', 'uploadedBy'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao enviar documento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        $documento = Documento::with(['tipoDocumento', 'documentavel', 'uploadedBy'])
            ->findOrFail($id);

        return response()->json($documento);
    }

    public function download(string $id)
    {
        $documento = Documento::findOrFail($id);

        if (!Storage::disk('public')->exists($documento->caminho)) {
            return response()->json([
                'message' => 'Ficheiro não encontrado'
            ], 404);
        }

        return Storage::disk('public')->download($documento->caminho, $documento->nome_original);
    }

    public function destroy(string $id)
    {
        $documento = Documento::findOrFail($id);

        // Remover ficheiro do storage
        if (Storage::disk('public')->exists($documento->caminho)) {
            Storage::disk('public')->delete($documento->caminho);
        }

        $documento->delete();

        return response()->json([
            'message' => 'Documento removido com sucesso'
        ]);
    }

    public function validar(Request $request, string $id)
    {
        $documento = Documento::findOrFail($id);

        $validated = $request->validate([
            'estado' => 'required|in:valido,invalido,expirado',
            'observacoes' => 'nullable|string',
        ]);

        $documento->update($validated);

        return response()->json([
            'message' => 'Documento validado com sucesso',
            'data' => $documento
        ]);
    }
}
