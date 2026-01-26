<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('faturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('membro_id')->constrained('membros')->onDelete('cascade');
            $table->date('data_emissao');
            $table->string('mes', 7)->nullable(); // YYYY-MM
            $table->date('data_inicio_periodo')->nullable();
            $table->date('data_fim_periodo')->nullable();
            $table->decimal('valor_total', 10, 2);
            $table->string('status_cache')->nullable(); // pendente/parcial/pago/atraso
            $table->string('numero_recibo')->nullable();
            $table->string('referencia_pagamento')->nullable();
            $table->foreignId('centro_custo_id')->nullable()->constrained('centros_custo')->onDelete('set null');
            $table->timestamps();

            $table->index('club_id');
            $table->index('membro_id');
            $table->index('mes');
            $table->index('data_emissao');
            $table->index('centro_custo_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faturas');
    }
};
