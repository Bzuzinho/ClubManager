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
        Schema::create('relacoes_pessoas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pessoa_origem_id')->constrained('pessoas')->cascadeOnDelete();
            $table->foreignId('pessoa_destino_id')->constrained('pessoas')->cascadeOnDelete();
            $table->string('tipo_relacao'); // Pai, Mãe, Irmão, Cônjuge, Filho, Outro
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            // Evitar relações duplicadas
            $table->unique(['pessoa_origem_id', 'pessoa_destino_id', 'tipo_relacao'], 'relacao_unica');
            $table->index('tipo_relacao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relacoes_pessoas');
    }
};
