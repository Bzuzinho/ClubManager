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
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_evento_id')->nullable()->constrained('tipos_evento')->nullOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fim')->nullable();
            $table->string('local')->nullable();
            $table->string('morada_completa')->nullable();
            $table->decimal('preco', 8, 2)->nullable()->default(0);
            $table->integer('vagas_totais')->nullable(); // null = ilimitado
            $table->integer('vagas_disponiveis')->nullable();
            $table->date('data_limite_inscricao')->nullable();
            $table->boolean('publico')->default(true); // Se é visível para não membros
            $table->boolean('requer_aprovacao')->default(false);
            $table->enum('estado', ['rascunho', 'publicado', 'em_curso', 'finalizado', 'cancelado'])->default('rascunho');
            $table->string('imagem')->nullable();
            $table->text('observacoes')->nullable();
            $table->foreignId('criado_por')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('data_inicio');
            $table->index('estado');
            $table->index('publico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
