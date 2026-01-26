<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('lancamentos_financeiros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->date('data');
            $table->text('descricao');
            $table->string('tipo'); // receita/despesa
            $table->decimal('valor', 10, 2);
            $table->foreignId('centro_custo_id')->nullable()->constrained('centros_custo')->onDelete('set null');
            $table->foreignId('fatura_id')->nullable()->constrained('faturas')->onDelete('set null');
            $table->foreignId('membro_id')->nullable()->constrained('membros')->onDelete('set null');
            $table->timestamps();

            $table->index('club_id');
            $table->index('data');
            $table->index('tipo');
            $table->index('centro_custo_id');
            $table->index('fatura_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lancamentos_financeiros');
    }
};
