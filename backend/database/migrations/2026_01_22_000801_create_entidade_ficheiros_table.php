<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('entidade_ficheiros', function (Blueprint $table) {
            $table->id();
            $table->string('entidade_type');
            $table->unsignedBigInteger('entidade_id');
            $table->foreignId('ficheiro_id')->constrained('ficheiros')->onDelete('cascade');
            $table->string('tipo');
            $table->date('data_documento')->nullable();
            $table->timestamps();

            $table->unique(['entidade_type', 'entidade_id', 'ficheiro_id', 'tipo'], 'entidade_ficheiros_unique');
            $table->index(['entidade_type', 'entidade_id']);
            $table->index('ficheiro_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entidade_ficheiros');
    }
};
