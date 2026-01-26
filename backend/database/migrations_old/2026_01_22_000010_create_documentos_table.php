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
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentavel'); // documentavel_type, documentavel_id (polimórfica)
            $table->foreignId('tipo_documento_id')->constrained('tipos_documento');
            $table->string('nome_original'); // Nome do ficheiro original
            $table->string('nome_ficheiro'); // Nome guardado no sistema
            $table->string('caminho'); // Caminho completo no storage
            $table->string('mime_type')->nullable();
            $table->integer('tamanho')->nullable(); // bytes
            $table->date('data_emissao')->nullable();
            $table->date('data_validade')->nullable();
            $table->date('data_upload');
            $table->enum('estado', ['valido', 'expirado', 'pendente_validacao', 'rejeitado'])->default('pendente_validacao');
            $table->text('observacoes')->nullable();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['documentavel_type', 'documentavel_id']);
            $table->index('data_validade');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
