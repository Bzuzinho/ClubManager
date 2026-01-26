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
        Schema::create('faturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membro_id')->constrained('membros')->cascadeOnDelete();
            $table->string('numero_fatura')->unique(); // FAT-2026-001
            $table->date('data_emissao');
            $table->date('data_vencimento');
            $table->decimal('valor_total', 10, 2);
            $table->decimal('valor_pago', 10, 2)->default(0);
            $table->decimal('valor_pendente', 10, 2);
            $table->enum('estado', ['pendente', 'paga', 'parcialmente_paga', 'vencida', 'cancelada'])->default('pendente');
            $table->enum('tipo', ['mensalidade', 'inscricao', 'evento', 'multa', 'outro'])->default('mensalidade');
            $table->string('referencia_mb')->nullable(); // Referência Multibanco
            $table->text('observacoes')->nullable();
            $table->foreignId('emitida_por')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('numero_fatura');
            $table->index(['membro_id', 'estado']);
            $table->index('data_vencimento');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faturas');
    }
};
