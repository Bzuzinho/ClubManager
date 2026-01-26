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
        Schema::create('centros_custo', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique(); // Modalidade Futebol, Eventos, Manutenção, etc
            $table->string('codigo')->unique();
            $table->text('descricao')->nullable();
            $table->foreignId('responsavel_id')->nullable()->constrained('membros')->nullOnDelete();
            $table->decimal('orcamento_anual', 10, 2)->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            $table->index('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('centros_custo');
    }
};
