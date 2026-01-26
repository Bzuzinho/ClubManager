<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('movimentos_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materiais')->onDelete('cascade');
            $table->string('tipo'); // entrada/saida/ajuste
            $table->integer('quantidade');
            $table->dateTime('data');
            $table->string('referencia')->nullable();
            $table->foreignId('membro_id')->nullable()->constrained('membros')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('club_id');
            $table->index('material_id');
            $table->index('data');
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimentos_stock');
    }
};
