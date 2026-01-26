<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('dados_desportivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('atleta_id')->constrained('atletas')->onDelete('cascade');
            $table->string('num_federacao')->nullable();
            $table->string('numero_pmb')->nullable();
            $table->date('data_inscricao')->nullable();
            $table->foreignId('escalao_atual_id')->nullable()->constrained('escaloes')->onDelete('set null');
            $table->date('data_atestado_medico')->nullable();
            $table->text('informacoes_medicas')->nullable();
            $table->timestamps();

            $table->unique(['club_id', 'atleta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dados_desportivos');
    }
};
