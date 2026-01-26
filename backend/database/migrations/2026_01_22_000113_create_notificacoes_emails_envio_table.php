<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('notificacoes_emails_envio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('notificacao_config_id')->constrained('notificacoes_config')->onDelete('cascade');
            $table->string('email_envio');
            $table->string('nome_remetente')->nullable();
            $table->boolean('ativo')->default(true);
            $table->integer('prioridade')->default(1);
            $table->timestamps();

            $table->unique(['notificacao_config_id', 'email_envio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacoes_emails_envio');
    }
};
