<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('eventos_tipos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('nome');
            $table->timestamps();

            $table->unique(['club_id', 'nome']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos_tipos');
    }
};
