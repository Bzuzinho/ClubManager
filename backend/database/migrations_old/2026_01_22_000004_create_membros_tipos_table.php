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
        Schema::create('membros_tipos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membro_id')->constrained('membros')->cascadeOnDelete();
            $table->foreignId('tipo_membro_id')->constrained('tipos_membro')->cascadeOnDelete();
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->boolean('ativo')->default(true);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            // Um membro pode ter o mesmo tipo apenas uma vez ativo
            $table->index(['membro_id', 'tipo_membro_id', 'ativo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membros_tipos');
    }
};
