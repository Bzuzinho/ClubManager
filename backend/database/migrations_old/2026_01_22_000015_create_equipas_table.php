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
        Schema::create('equipas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modalidade_id')->constrained('modalidades')->cascadeOnDelete();
            $table->foreignId('escalao_id')->nullable()->constrained('escaloes')->nullOnDelete();
            $table->string('nome'); // Ex: Seniores A, Juniores B, etc
            $table->string('codigo')->unique(); // Ex: FUT-SEN-A
            $table->enum('genero', ['masculino', 'feminino', 'misto'])->default('misto');
            $table->string('temporada'); // Ex: 2025/2026
            $table->foreignId('treinador_principal_id')->nullable()->constrained('membros')->nullOnDelete();
            $table->string('local_treino')->nullable();
            $table->text('horario_treino')->nullable(); // JSON ou texto livre
            $table->enum('estado', ['ativa', 'inativa', 'suspensa'])->default('ativa');
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['modalidade_id', 'escalao_id']);
            $table->index('temporada');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipas');
    }
};
