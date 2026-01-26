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
        Schema::create('modalidades', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique(); // Futebol, Futsal, Basquetebol, etc
            $table->string('codigo')->unique(); // FUT, FUTS, BASQ, etc
            $table->text('descricao')->nullable();
            $table->string('icone')->nullable(); // Nome do ícone ou path
            $table->string('cor')->nullable(); // Cor associada à modalidade (hex)
            $table->boolean('ativa')->default(true);
            $table->integer('ordem')->default(0);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('codigo');
            $table->index('ativa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modalidades');
    }
};
