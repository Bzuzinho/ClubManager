<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('dados_financeiros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('membro_id')->constrained('membros')->onDelete('cascade');
            $table->foreignId('mensalidade_id')->nullable()->constrained('mensalidades')->onDelete('set null');
            $table->integer('dia_cobranca')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->unique(['club_id', 'membro_id']);
            $table->index('club_id');
            $table->index('membro_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dados_financeiros');
    }
};
