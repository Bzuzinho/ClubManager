<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('dados_pessoais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('nome_completo');
            $table->date('data_nascimento')->nullable();
            $table->string('nif')->nullable();
            $table->string('cc')->nullable();
            $table->text('morada')->nullable();
            $table->string('codigo_postal')->nullable();
            $table->string('localidade')->nullable();
            $table->string('nacionalidade')->nullable();
            $table->string('sexo')->nullable();
            $table->string('contacto_telefonico')->nullable();
            $table->string('email_secundario')->nullable();
            $table->timestamps();

            $table->index('nif');
            $table->index('data_nascimento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dados_pessoais');
    }
};
