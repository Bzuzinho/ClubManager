<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('encarregados_educacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pessoa_id')->unique()->constrained('pessoas')->cascadeOnDelete();
            $table->string('telemovel_alternativo', 20)->nullable();
            $table->string('email_alternativo')->nullable();
            $table->string('profissao')->nullable();
            $table->string('local_trabalho')->nullable();
            $table->string('telefone_trabalho', 20)->nullable();
            $table->boolean('contacto_emergencia')->default(true);
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encarregados_educacao');
    }
};
