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
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fatura_id')->constrained('faturas')->cascadeOnDelete();
            $table->foreignId('metodo_pagamento_id')->constrained('metodos_pagamento');
            $table->string('numero_pagamento')->unique(); // PAG-2026-001
            $table->date('data_pagamento');
            $table->decimal('valor', 10, 2);
            $table->string('referencia')->nullable(); // Referência da transação
            $table->string('comprovativo')->nullable(); // Path para ficheiro
            $table->enum('estado', ['pendente', 'confirmado', 'rejeitado'])->default('pendente');
            $table->text('observacoes')->nullable();
            $table->foreignId('registado_por')->constrained('users');
            $table->foreignId('confirmado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('data_confirmacao')->nullable();
            $table->timestamps();
            
            $table->index('numero_pagamento');
            $table->index(['fatura_id', 'estado']);
            $table->index('data_pagamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
