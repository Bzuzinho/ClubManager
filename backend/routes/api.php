<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\MembroController;
use App\Http\Controllers\AtletaController;
use App\Http\Controllers\EquipaController;
use App\Http\Controllers\TreinoController;
use App\Http\Controllers\CompeticaoController;
use App\Http\Controllers\FaturaController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\DocumentoController;

/*
|--------------------------------------------------------------------------
| API Routes - ClubManager
|--------------------------------------------------------------------------
*/

// Autenticação
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Rotas protegidas por autenticação
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Pessoas
    Route::apiResource('pessoas', PessoaController::class);
    Route::post('pessoas/{id}/restore', [PessoaController::class, 'restore']);

    // Membros
    Route::apiResource('membros', MembroController::class);
    Route::put('membros/{id}/tipos', [MembroController::class, 'updateTipos']);

    // Atletas
    Route::apiResource('atletas', AtletaController::class);
    Route::put('atletas/{id}/equipas', [AtletaController::class, 'updateEquipas']);
    Route::get('atletas/{id}/estatisticas', [AtletaController::class, 'estatisticas']);

    // Equipas
    Route::apiResource('equipas', EquipaController::class);
    Route::get('equipas/{id}/plantel', [EquipaController::class, 'plantel']);
    Route::post('equipas/{id}/atletas', [EquipaController::class, 'adicionarAtletas']);

    // Treinos
    Route::apiResource('treinos', TreinoController::class);
    Route::post('treinos/{id}/presencas', [TreinoController::class, 'registarPresencas']);
    Route::get('treinos/{id}/estatisticas-presenca', [TreinoController::class, 'estatisticasPresenca']);

    // Competições
    Route::apiResource('competicoes', CompeticaoController::class);
    Route::post('competicoes/{id}/convocar', [CompeticaoController::class, 'convocar']);

    // Faturas
    Route::apiResource('faturas', FaturaController::class);
    Route::post('faturas/{id}/cancelar', [FaturaController::class, 'cancelar']);

    // Pagamentos
    Route::post('pagamentos', [PagamentoController::class, 'store']);
    Route::post('pagamentos/{id}/confirmar', [PagamentoController::class, 'confirmar']);
    Route::post('pagamentos/{id}/cancelar', [PagamentoController::class, 'cancelar']);

    // Eventos
    Route::apiResource('eventos', EventoController::class);
    Route::post('eventos/{id}/inscrever', [EventoController::class, 'inscrever']);

    // Documentos
    Route::apiResource('documentos', DocumentoController::class)->except(['update']);
    Route::get('documentos/{id}/download', [DocumentoController::class, 'download']);
    Route::post('documentos/{id}/validar', [DocumentoController::class, 'validar']);
});
