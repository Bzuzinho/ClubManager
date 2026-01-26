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
        Schema::create('dados_desportivos_atleta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atleta_id')->constrained('atletas')->cascadeOnDelete();
            $table->foreignId('equipa_id')->nullable()->constrained('equipas')->nullOnDelete();
            $table->string('temporada'); // Ex: 2025/2026
            // Estatísticas
            $table->integer('jogos_realizados')->default(0);
            $table->integer('jogos_titular')->default(0);
            $table->integer('minutos_jogados')->default(0);
            $table->integer('golos')->default(0);
            $table->integer('assistencias')->default(0);
            $table->integer('cartoes_amarelos')->default(0);
            $table->integer('cartoes_vermelhos')->default(0);
            $table->integer('treinos_presentes')->default(0);
            $table->integer('treinos_totais')->default(0);
            // Performance
            $table->decimal('percentagem_presenca', 5, 2)->nullable();
            $table->decimal('media_golos', 5, 2)->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            $table->unique(['atleta_id', 'equipa_id', 'temporada']);
            $table->index('temporada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dados_desportivos_atleta');
    }
};
