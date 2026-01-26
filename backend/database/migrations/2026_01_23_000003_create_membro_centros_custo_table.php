<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    
    public function up(): void
    {
        Schema::create('membro_centros_custo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('membro_id')->constrained('membros')->onDelete('cascade');
            $table->foreignId('centro_custo_id')->constrained('centros_custo')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['club_id', 'membro_id', 'centro_custo_id'], 'membro_cc_unique');
            $table->index('club_id');
            $table->index('membro_id');
            $table->index('centro_custo_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membro_centros_custo');
    }
};
