<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('presencas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('treino_id')->constrained('treinos')->onDelete('cascade');
            $table->foreignId('membro_id')->constrained('membros')->onDelete('cascade');
            $table->string('estado')->default('presente'); // presente/falta/justificada
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->unique(['club_id', 'treino_id', 'membro_id']);
            $table->index('club_id');
            $table->index('treino_id');
            $table->index('membro_id');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presencas');
    }
};
