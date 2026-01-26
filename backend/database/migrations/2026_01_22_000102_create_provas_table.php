<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('provas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('nome');
            $table->integer('distancia_m')->nullable();
            $table->string('modalidade')->nullable();
            $table->boolean('individual')->default(true);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index('club_id');
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provas');
    }
};
