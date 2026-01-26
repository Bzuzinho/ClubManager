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
        Schema::create('competicoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modalidade_id')->constrained('modalidades')->cascadeOnDelete();
            $table->foreignId('equipa_casa_id')->nullable()->constrained('equipas')->nullOnDelete();
            $table->string('adversario')->nullable(); // Nome do adversário
            $table->enum('tipo', ['jogo', 'torneio', 'amigavel', 'campeonato', 'taca'])->default('jogo');
            $table->date('data');
            $table->time('hora')->nullable();
            $table->string('local');
            $table->boolean('casa')->default(true); // true = em casa, false = fora
            $table->string('competicao')->nullable(); // Nome da competição/liga
            $table->string('jornada')->nullable(); // Jornada ou fase
            $table->enum('estado', ['agendado', 'em_curso', 'finalizado', 'cancelado', 'adiado'])->default('agendado');
            $table->integer('golos_favor')->nullable();
            $table->integer('golos_contra')->nullable();
            $table->enum('resultado', ['vitoria', 'empate', 'derrota'])->nullable();
            $table->text('relatorio')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['equipa_casa_id', 'data']);
            $table->index('estado');
            $table->index('tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competicoes');
    }
};
