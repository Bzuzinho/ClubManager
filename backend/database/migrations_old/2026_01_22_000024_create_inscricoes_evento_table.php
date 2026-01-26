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
        Schema::create('inscricoes_evento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->cascadeOnDelete();
            $table->foreignId('membro_id')->constrained('membros')->cascadeOnDelete();
            $table->enum('estado', ['pendente', 'confirmada', 'cancelada', 'em_lista_espera'])->default('pendente');
            $table->date('data_inscricao');
            $table->date('data_confirmacao')->nullable();
            $table->boolean('pago')->default(false);
            $table->decimal('valor_pago', 8, 2)->nullable();
            $table->integer('numero_acompanhantes')->default(0);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            $table->unique(['evento_id', 'membro_id']);
            $table->index('estado');
            $table->index('data_inscricao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscricoes_evento');
    }
};
