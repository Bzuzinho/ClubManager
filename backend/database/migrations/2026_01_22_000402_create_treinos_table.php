<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('treinos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('grupo_id')->constrained('grupos')->onDelete('cascade');
            $table->foreignId('microciclo_id')->nullable()->constrained('microciclos')->onDelete('set null');
            $table->dateTime('data_agendada');
            $table->text('descricao')->nullable();
            $table->longText('conteudo')->nullable();
            $table->string('estado')->default('planeado'); // planeado/realizado/fechado
            $table->timestamps();

            $table->index('club_id');
            $table->index('grupo_id');
            $table->index('data_agendada');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treinos');
    }
};
