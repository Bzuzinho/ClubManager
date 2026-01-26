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
        Schema::create('atletas_encarregados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atleta_id')->constrained('atletas')->cascadeOnDelete();
            $table->foreignId('encarregado_id')->constrained('encarregados_educacao')->cascadeOnDelete();
            $table->string('grau_parentesco'); // Pai, Mãe, Tutor, Avô, Avó, Outro
            $table->boolean('principal')->default(false); // Encarregado principal
            $table->boolean('autorizado_levantar')->default(true); // Pode buscar o atleta
            $table->boolean('receber_notificacoes')->default(true);
            $table->timestamps();
            
            $table->unique(['atleta_id', 'encarregado_id']);
            $table->index('principal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atletas_encarregados');
    }
};
