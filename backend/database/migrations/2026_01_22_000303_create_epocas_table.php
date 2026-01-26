<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('epocas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('nome');
            $table->string('ano_temporada');
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->string('estado')->default('ativa'); // ativa/fechada/futura
            $table->timestamps();

            $table->unique(['club_id', 'ano_temporada']);
            $table->index('club_id');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('epocas');
    }
};
