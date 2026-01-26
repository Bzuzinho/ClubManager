<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ClubSwitchController;
use App\Http\Controllers\Api\MembrosController;
use App\Http\Controllers\Api\ConfiguracaoController;
use App\Http\Controllers\Api\FaturasController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\MembroController;
use App\Http\Controllers\TipoMembroController;
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

    // === NOVAS ROTAS (Arquitetura Refatorada) ===
    
    // Gestão de Clubes
    Route::prefix('clubs')->group(function () {
        Route::get('/', [ClubSwitchController::class, 'index']);
        Route::post('/switch', [ClubSwitchController::class, 'switch']);
        Route::get('/active', [ClubSwitchController::class, 'active']);
        Route::post('/clear', [ClubSwitchController::class, 'clear']);
    });

    // Rotas com contexto de clube (requerem clube ativo)
    Route::middleware('ensure.club.context')->group(function () {
        // Membros (nova versão)
        Route::apiResource('v2/membros', MembrosController::class)->names([
            'index' => 'v2.membros.index',
            'store' => 'v2.membros.store',
            'show' => 'v2.membros.show',
            'update' => 'v2.membros.update',
            'destroy' => 'v2.membros.destroy',
        ]);

        // Configuração de Utilizador
        Route::prefix('v2/configuracao')->name('v2.configuracao.')->group(function () {
            Route::get('/{userId}', [ConfiguracaoController::class, 'show'])->name('show');
            Route::put('/{userId}', [ConfiguracaoController::class, 'update'])->name('update');
            Route::post('/{userId}/reenviar-senha', [ConfiguracaoController::class, 'reenviarRecuperacaoSenha'])->name('reenviar-senha');
            Route::post('/{userId}/alterar-senha', [ConfiguracaoController::class, 'alterarSenha'])->name('alterar-senha');
        });

        // Faturas (nova versão)
        Route::prefix('v2/faturas')->name('v2.faturas.')->group(function () {
            Route::get('/', [FaturasController::class, 'index'])->name('index');
            Route::post('/', [FaturasController::class, 'store'])->name('store');
            Route::get('/{id}', [FaturasController::class, 'show'])->name('show');
            Route::post('/gerar-mensalidades', [FaturasController::class, 'gerarMensalidades'])->name('gerar-mensalidades');
            Route::post('/{id}/itens', [FaturasController::class, 'adicionarItem'])->name('adicionar-item');
            Route::post('/{id}/pagamentos', [FaturasController::class, 'registarPagamento'])->name('registar-pagamento');
        });

        // Conta Corrente
        Route::get('/v2/membros/{membroId}/conta-corrente', [FaturasController::class, 'contaCorrente'])->name('v2.membros.conta-corrente');
        Route::get('/v2/membros/{membroId}/resumo-financeiro', [FaturasController::class, 'resumoFinanceiro'])->name('v2.membros.resumo-financeiro');
    });

    // === ROTAS ANTIGAS (manter compatibilidade) ===

    // Pessoas - COMENTADO: tabela não existe no PostgreSQL
    // Route::apiResource('pessoas', PessoaController::class);
    // Route::post('pessoas/{id}/restore', [PessoaController::class, 'restore']);

    // Membros - LEGACY: usar /v2/membros
    Route::apiResource('membros', MembroController::class);
    Route::put('membros/{id}/tipos', [MembroController::class, 'updateTipos']);

    // Tipos de Membro - COMENTADO: usar configuração ou v2
    // Route::get('tipos-membro', [TipoMembroController::class, 'index']);
    // Route::get('tipos-membro/{id}', [TipoMembroController::class, 'show']);

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
