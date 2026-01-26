<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('nome');
            $table->foreignId('escalao_id')->nullable()->constrained('escaloes')->onDelete('set null');
            $table->foreignId('treinador_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('horario')->nullable();
            $table->string('local')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->unique(['club_id', 'nome']);
            $table->index('club_id');
            $table->index('escalao_id');
            $table->index('treinador_user_id');
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
