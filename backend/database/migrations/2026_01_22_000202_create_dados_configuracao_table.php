<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('dados_configuracao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('rgpd')->default(false);
            $table->date('data_rgpd')->nullable();
            $table->boolean('consentimento')->default(false);
            $table->date('data_consentimento')->nullable();
            $table->boolean('afiliacao')->default(false);
            $table->date('data_afiliacao')->nullable();
            $table->boolean('declaracao_transporte')->default(false);
            $table->string('email_utilizador')->nullable();
            $table->timestamps();

            $table->unique(['club_id', 'user_id']);
            $table->index('club_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dados_configuracao');
    }
};
