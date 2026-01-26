<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('macrociclos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('epoca_id')->constrained('epocas')->onDelete('cascade');
            $table->string('nome');
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->text('objetivo')->nullable();
            $table->timestamps();

            $table->index('club_id');
            $table->index('epoca_id');
            $table->index('data_inicio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('macrociclos');
    }
};
