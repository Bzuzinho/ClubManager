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
        Schema::create('categorias_movimento', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->string('codigo')->unique();
            $table->enum('tipo', ['receita', 'despesa']);
            $table->text('descricao')->nullable();
            $table->string('cor')->nullable();
            $table->boolean('ativo')->default(true);
            $table->integer('ordem')->default(0);
            $table->timestamps();
            
            $table->index(['tipo', 'codigo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias_movimento');
    }
};
