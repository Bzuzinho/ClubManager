<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('campanhas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('nome');
            $table->string('canal'); // email/sms
            $table->foreignId('segmento_id')->nullable()->constrained('segmentos')->onDelete('set null');
            $table->dateTime('agendado_para')->nullable();
            $table->string('estado')->default('rascunho'); // rascunho/agendada/enviada/cancelada
            $table->timestamps();

            $table->index('club_id');
            $table->index('canal');
            $table->index('estado');
            $table->index('agendado_para');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campanhas');
    }
};
