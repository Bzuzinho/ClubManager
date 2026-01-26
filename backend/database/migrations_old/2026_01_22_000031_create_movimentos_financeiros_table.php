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
        Schema::create('movimentos_financeiros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centro_custo_id')->nullable()->constrained('centros_custo')->nullOnDelete();
            $table->foreignId('categoria_movimento_id')->constrained('categorias_movimento');
            $table->enum('tipo', ['receita', 'despesa']);
            $table->string('numero_movimento')->unique(); // MOV-2026-001
            $table->date('data_movimento');
            $table->decimal('valor', 10, 2);
            $table->string('descricao');
            $table->text('observacoes')->nullable();
            $table->foreignId('pagamento_id')->nullable()->constrained('pagamentos')->nullOnDelete(); // Ligação a pagamentos se aplicável
            $table->string('documento_comprovativo')->nullable(); // Path para ficheiro
            $table->foreignId('registado_por')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('numero_movimento');
            $table->index(['tipo', 'data_movimento']);
            $table->index('data_movimento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimentos_financeiros');
    }
};
