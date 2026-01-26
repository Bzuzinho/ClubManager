<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('user_tipos_utilizador', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tipo_utilizador_id')->constrained('tipos_utilizador')->onDelete('cascade');
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->unique(['club_id', 'user_id', 'tipo_utilizador_id', 'data_inicio'], 'user_tipos_unique');
            $table->index('club_id');
            $table->index('user_id');
            $table->index('tipo_utilizador_id');
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_tipos_utilizador');
    }
};
