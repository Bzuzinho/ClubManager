<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('relacoes_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('user_origem_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_destino_id')->constrained('users')->onDelete('cascade');
            $table->string('tipo_relacao'); // encarregado/atleta/etc
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->unique(['club_id', 'user_origem_id', 'user_destino_id', 'tipo_relacao', 'data_inicio'], 'relacoes_users_unique');
            $table->index('club_id');
            $table->index('user_origem_id');
            $table->index('user_destino_id');
            $table->index('tipo_relacao');
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relacoes_users');
    }
};
