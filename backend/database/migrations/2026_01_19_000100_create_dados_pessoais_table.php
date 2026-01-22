<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dados_pessoais', function (Blueprint $table) {
            $table->id();

            // 1:1 com users (se existir registo aqui, é membro)
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            // Identificação de sócio / estado do membro
            $table->string('numero_socio')->unique();
            $table->enum('estado', ['ativo', 'inativo', 'suspenso'])->default('ativo');

            // Tipo do membro (atleta, encarregado, socio, etc.)
            $table->string('tipo_utilizador')->index();

            // Regras menor / EE / educando
            $table->boolean('menor')->default(false);

            $table->foreignId('encarregado_educacao_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('educando_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Campos base (expande depois conforme o teu documento)
            $table->string('telemovel')->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('nif', 20)->nullable();

            $table->timestamps();

            $table->index('estado');
            $table->index('menor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dados_pessoais');
    }
};
