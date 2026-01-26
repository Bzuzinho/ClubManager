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
        Schema::create('atletas_equipas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atleta_id')->constrained('atletas')->cascadeOnDelete();
            $table->foreignId('equipa_id')->constrained('equipas')->cascadeOnDelete();
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->string('numero_camisola')->nullable();
            $table->string('posicao')->nullable();
            $table->boolean('titular')->default(false); // Se é titular ou suplente
            $table->boolean('capitao')->default(false);
            $table->boolean('ativo')->default(true);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            $table->index(['atleta_id', 'equipa_id', 'ativo']);
            $table->index('data_inicio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atletas_equipas');
    }
};
