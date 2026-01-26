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
        Schema::create('tipos_evento', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique(); // Festa, Torneio, Assembleia, etc
            $table->string('codigo')->unique(); // FESTA, TORNEIO, ASSEMBLEIA
            $table->text('descricao')->nullable();
            $table->string('cor')->nullable(); // Cor no calendário
            $table->string('icone')->nullable();
            $table->boolean('requer_inscricao')->default(false);
            $table->boolean('ativo')->default(true);
            $table->integer('ordem')->default(0);
            $table->timestamps();
            
            $table->index('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_evento');
    }
};
