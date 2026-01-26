<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('membros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('numero_socio')->nullable();
            $table->string('estado')->default('ativo'); // ativo/inativo/suspenso
            $table->date('data_adesao')->nullable();
            $table->date('data_fim')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->unique(['club_id', 'user_id']);
            $table->unique(['club_id', 'numero_socio']);
            $table->index('club_id');
            $table->index('estado');
            $table->index('data_adesao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membros');
    }
};
