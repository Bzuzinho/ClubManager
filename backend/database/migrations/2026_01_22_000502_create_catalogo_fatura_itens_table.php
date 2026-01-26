<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('catalogo_fatura_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('descricao');
            $table->decimal('valor_unitario', 10, 2);
            $table->decimal('imposto_percentual', 5, 2)->default(0);
            $table->string('tipo')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index('club_id');
            
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogo_fatura_itens');
    }
};
