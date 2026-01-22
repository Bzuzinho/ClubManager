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
        Schema::create('tipos_membro', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique(); // Ex: Atleta, Sócio, Staff, Encarregado
            $table->string('codigo')->unique(); // ATLETA, SOCIO, STAFF, ENCARREGADO
            $table->text('descricao')->nullable();
            $table->decimal('mensalidade', 8, 2)->default(0);
            $table->integer('limite_modalidades')->default(1); // Quantos desportos pode praticar
            $table->boolean('requer_encarregado')->default(false); // Se precisa de encarregado (menores)
            $table->boolean('pode_competir')->default(false); // Se pode participar em competições
            $table->boolean('ativo')->default(true);
            $table->integer('ordem')->default(0); // Para ordenação na UI
            $table->timestamps();
            
            $table->index('codigo');
            $table->index('ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_membro');
    }
};
