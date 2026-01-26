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
        Schema::create('presencas_treino', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treino_id')->constrained('treinos')->cascadeOnDelete();
            $table->foreignId('atleta_id')->constrained('atletas')->cascadeOnDelete();
            $table->enum('estado', ['presente', 'ausente', 'justificado', 'atrasado', 'saiu_mais_cedo'])->default('ausente');
            $table->time('hora_chegada')->nullable();
            $table->time('hora_saida')->nullable();
            $table->text('justificacao')->nullable();
            $table->text('observacoes')->nullable();
            $table->foreignId('registado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            // Um atleta só pode ter um registo de presença por treino
            $table->unique(['treino_id', 'atleta_id']);
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presencas_treino');
    }
};
