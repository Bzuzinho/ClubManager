<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('notificacoes_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('tipo_id')->constrained('notificacoes_tipos')->onDelete('cascade');
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->unique(['club_id', 'tipo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacoes_config');
    }
};
