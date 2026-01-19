<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pessoas', function (Blueprint $table) {
            $table->id();

            $table->string('nome_completo');
            $table->date('data_nascimento')->nullable();
            $table->string('nif', 20)->nullable();
            $table->string('cc', 30)->nullable();

            $table->enum('sexo', ['masculino', 'feminino'])->nullable();
            $table->string('nacionalidade')->nullable();

            $table->string('contacto_telefonico')->nullable();
            $table->string('email_secundario')->nullable();

            $table->string('morada')->nullable();
            $table->string('codigo_postal', 20)->nullable();
            $table->string('localidade')->nullable();

            $table->string('estado_civil')->nullable();
            $table->string('ocupacao')->nullable();
            $table->string('empresa')->nullable();
            $table->string('escola')->nullable();

            $table->integer('numero_irmaos')->nullable();
            $table->boolean('menor')->default(false);

            $table->string('foto_perfil')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pessoas');
    }
};
