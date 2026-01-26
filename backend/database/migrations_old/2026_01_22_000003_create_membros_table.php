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
        Schema::create('membros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pessoa_id')->unique()->constrained('pessoas')->cascadeOnDelete();
            $table->string('numero_socio')->unique();
            $table->enum('estado', ['ativo', 'inativo', 'suspenso', 'pendente'])->default('pendente');
            $table->date('data_inscricao');
            $table->date('data_inicio')->nullable(); // Data em que ficou ativo
            $table->date('data_fim')->nullable();
            $table->string('motivo_inativacao')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('numero_socio');
            $table->index('estado');
            $table->index('data_inscricao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membros');
    }
};
