<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('resultados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->foreignId('atleta_id')->constrained('atletas')->onDelete('cascade');
            $table->foreignId('prova_id')->constrained('provas')->onDelete('cascade');
            $table->foreignId('epoca_id')->nullable()->constrained('epocas')->onDelete('set null');
            $table->string('piscina')->nullable();
            $table->string('tempo')->nullable();
            $table->integer('classificacao')->nullable();
            $table->integer('pontos')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index('club_id');
            $table->index('evento_id');
            $table->index('atleta_id');
            $table->index('prova_id');
            $table->index('epoca_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultados');
    }
};
