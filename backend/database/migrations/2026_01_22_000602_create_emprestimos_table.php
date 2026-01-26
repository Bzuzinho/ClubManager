<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('emprestimos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materiais')->onDelete('cascade');
            $table->foreignId('membro_id')->constrained('membros')->onDelete('cascade');
            $table->integer('quantidade');
            $table->date('data_saida');
            $table->date('data_devolucao')->nullable();
            $table->string('estado')->default('ativo'); // ativo/devolvido/perdido
            $table->timestamps();

            $table->index('club_id');
            $table->index('material_id');
            $table->index('membro_id');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emprestimos');
    }
};
