<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('tipo_id')->constrained('eventos_tipos')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->dateTime('data_inicio');
            $table->dateTime('data_fim')->nullable();
            $table->string('local')->nullable();
            $table->text('transporte')->nullable();
            $table->text('logistica')->nullable();
            $table->foreignId('patrono_id')->nullable()->constrained('patronos')->onDelete('set null');
            $table->foreignId('centro_custo_id')->nullable()->constrained('centros_custo')->onDelete('set null');
            $table->string('estado')->default('rascunho'); // rascunho/publicado/fechado
            $table->timestamps();

            $table->index('club_id');
            $table->index('tipo_id');
            $table->index('data_inicio');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
