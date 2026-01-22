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
        Schema::create('escaloes', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // Sub-8, Sub-10, Sub-12, etc
            $table->string('codigo')->unique(); // SUB8, SUB10, SUB12, etc
            $table->integer('idade_minima');
            $table->integer('idade_maxima');
            $table->integer('ano_nascimento_inicio')->nullable(); // Ex: 2018
            $table->integer('ano_nascimento_fim')->nullable(); // Ex: 2019
            $table->enum('genero', ['masculino', 'feminino', 'misto'])->default('misto');
            $table->text('descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->integer('ordem')->default(0);
            $table->timestamps();
            
            $table->index('codigo');
            $table->index(['idade_minima', 'idade_maxima']);
            $table->index('ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escaloes');
    }
};
