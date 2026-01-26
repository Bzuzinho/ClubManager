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
        Schema::create('pessoas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('nome_completo');
            $table->string('nif', 9)->unique()->nullable();
            $table->string('email')->unique();
            $table->string('telemovel', 20)->nullable();
            $table->string('telefone_fixo', 20)->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('nacionalidade')->nullable()->default('Portuguesa');
            $table->string('naturalidade')->nullable();
            $table->string('numero_identificacao')->nullable(); // CC/BI
            $table->date('validade_identificacao')->nullable();
            $table->string('morada')->nullable();
            $table->string('codigo_postal', 8)->nullable();
            $table->string('localidade')->nullable();
            $table->string('distrito')->nullable();
            $table->string('foto_perfil')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('nome_completo');
            $table->index('email');
            $table->index('nif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pessoas');
    }
};
