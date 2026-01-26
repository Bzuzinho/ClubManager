<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Desabilitar transações para PostgreSQL
    public $withinTransaction = false;
    
    public function up(): void
    {
        Schema::create('club_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('club_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role_no_clube')->nullable();
            $table->boolean('ativo')->default(true);
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->timestamps();

            $table->unique(['club_id', 'user_id', 'data_inicio']);
            $table->index(['club_id', 'user_id']);
        });
        
        // Adicionar foreign keys depois
        Schema::table('club_users', function (Blueprint $table) {
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_users');
    }
};
