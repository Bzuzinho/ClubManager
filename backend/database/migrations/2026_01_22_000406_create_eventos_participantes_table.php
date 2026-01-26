<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('eventos_participantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->foreignId('membro_id')->constrained('membros')->onDelete('cascade');
            $table->string('estado_confirmacao')->default('pendente'); // confirmado/pendente/recusado
            $table->text('justificacao')->nullable();
            $table->timestamps();

            $table->unique(['club_id', 'evento_id', 'membro_id']);
            $table->index('club_id');
            $table->index('evento_id');
            $table->index('membro_id');
            $table->index('estado_confirmacao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos_participantes');
    }
};
