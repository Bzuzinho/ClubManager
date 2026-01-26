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
        Schema::create('consentimentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pessoa_id')->constrained('pessoas')->cascadeOnDelete();
            $table->string('tipo'); // tratamento_dados, imagem, saude, transporte, etc
            $table->boolean('consentido')->default(false);
            $table->date('data_consentimento')->nullable();
            $table->foreignId('consentido_por')->nullable()->constrained('users'); // Quem deu o consentimento
            $table->text('observacoes')->nullable();
            $table->string('versao_termo')->nullable(); // Para rastrear versões dos termos RGPD
            $table->timestamps();
            
            // Uma pessoa só pode ter um consentimento de cada tipo ativo
            $table->unique(['pessoa_id', 'tipo']);
            $table->index('consentido');
            $table->index('data_consentimento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consentimentos');
    }
};
