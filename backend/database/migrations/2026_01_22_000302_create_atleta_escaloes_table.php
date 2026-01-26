<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('atleta_escaloes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('atleta_id')->constrained('atletas')->onDelete('cascade');
            $table->foreignId('escalao_id')->constrained('escaloes')->onDelete('cascade');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->timestamps();

            $table->unique(['club_id', 'atleta_id', 'escalao_id', 'data_inicio'], 'atleta_escaloes_unique');
            $table->index('club_id');
            $table->index('atleta_id');
            $table->index('escalao_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atleta_escaloes');
    }
};
