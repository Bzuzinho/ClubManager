<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('atletas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('membro_id')->constrained('membros')->onDelete('cascade');
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->unique(['club_id', 'membro_id']);
            $table->index('club_id');
            $table->index('membro_id');
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atletas');
    }
};
