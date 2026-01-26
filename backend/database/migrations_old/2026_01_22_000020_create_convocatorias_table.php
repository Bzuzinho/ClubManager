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
        Schema::create('convocatorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competicao_id')->constrained('competicoes')->cascadeOnDelete();
            $table->foreignId('atleta_id')->constrained('atletas')->cascadeOnDelete();
            $table->enum('estado', ['convocado', 'confirmado', 'ausente', 'justificado', 'lesionado'])->default('convocado');
            $table->boolean('titular')->default(false);
            $table->time('hora_concentracao')->nullable();
            $table->string('local_concentracao')->nullable();
            $table->text('observacoes')->nullable();
            $table->foreignId('convocado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('data_convocatoria');
            $table->timestamps();
            
            $table->unique(['competicao_id', 'atleta_id']);
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convocatorias');
    }
};
