<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('envios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('campanha_id')->constrained('campanhas')->onDelete('cascade');
            $table->foreignId('membro_id')->constrained('membros')->onDelete('cascade');
            $table->string('canal');
            $table->string('assunto')->nullable();
            $table->longText('conteudo_enviado')->nullable();
            $table->string('estado')->default('pendente'); // pendente/enviado/erro
            $table->string('provider_message_id')->nullable();
            $table->dateTime('enviado_em')->nullable();
            $table->timestamps();

            $table->index('club_id');
            $table->index('campanha_id');
            $table->index('membro_id');
            $table->index('estado');
            $table->index('enviado_em');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('envios');
    }
};
