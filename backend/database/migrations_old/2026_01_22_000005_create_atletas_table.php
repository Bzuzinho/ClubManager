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
        Schema::create('atletas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membro_id')->unique()->constrained('membros')->cascadeOnDelete();
            $table->boolean('ativo')->default(true);
            $table->string('numero_camisola')->nullable();
            $table->string('tamanho_equipamento')->nullable(); // XS, S, M, L, XL, XXL
            $table->decimal('altura', 5, 2)->nullable(); // em centímetros
            $table->decimal('peso', 5, 2)->nullable(); // em kg
            $table->enum('pe_dominante', ['direito', 'esquerdo', 'ambidestro'])->nullable();
            $table->string('posicao_preferida')->nullable();
            $table->text('observacoes_medicas')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('ativo');
            $table->index('numero_camisola');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atletas');
    }
};
