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
        Schema::create('historico_estados', function (Blueprint $table) {
            $table->id();
            $table->morphs('entidade'); // entidade_type, entidade_id (polimórfica)
            $table->string('estado_anterior')->nullable();
            $table->string('estado_novo');
            $table->string('motivo')->nullable();
            $table->text('observacoes')->nullable();
            $table->foreignId('alterado_por')->constrained('users');
            $table->timestamp('data_alteracao');
            $table->timestamps();
            
            $table->index(['entidade_type', 'entidade_id']);
            $table->index('data_alteracao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_estados');
    }
};
