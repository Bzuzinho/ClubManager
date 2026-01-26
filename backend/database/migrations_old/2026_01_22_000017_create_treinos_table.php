<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('treinos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipa_id')->constrained('equipas')->cascadeOnDelete();
            $table->date('data');
            $table->time('hora_inicio');
            $table->time('hora_fim')->nullable();
            $table->string('local');
            $table->enum('tipo', ['treino', 'jogo_treino', 'fisico', 'tatico', 'tecnico'])->default('treino');
            $table->text('objetivos')->nullable();
            $table->text('descricao')->nullable();
            $table->text('observacoes')->nullable();
            $table->foreignId('responsavel_id')->nullable()->constrained('membros')->nullOnDelete();
            $table->enum('estado', ['agendado', 'realizado', 'cancelado', 'adiado'])->default('agendado');
            $table->text('motivo_cancelamento')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['equipa_id', 'data']);
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treinos');
    }
};
