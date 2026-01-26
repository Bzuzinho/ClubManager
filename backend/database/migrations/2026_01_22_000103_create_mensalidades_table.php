<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('mensalidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('nome');
            $table->integer('regularidade_por_semana')->default(0);
            $table->foreignId('escalao_id')->nullable()->constrained('escaloes')->onDelete('set null');
            $table->decimal('valor', 10, 2);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index('club_id');
            $table->index('escalao_id');
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensalidades');
    }
};
