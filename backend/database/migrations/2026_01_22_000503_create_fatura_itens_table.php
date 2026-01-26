<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('fatura_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('fatura_id')->constrained('faturas')->onDelete('cascade');
            $table->foreignId('catalogo_item_id')->nullable()->constrained('catalogo_fatura_itens')->onDelete('set null');
            $table->string('descricao');
            $table->decimal('valor_unitario', 10, 2);
            $table->integer('quantidade')->default(1);
            $table->decimal('imposto_percentual', 5, 2)->default(0);
            $table->decimal('total_linha', 10, 2);
            $table->foreignId('centro_custo_id')->nullable()->constrained('centros_custo')->onDelete('set null');
            $table->timestamps();

            $table->index('club_id');
            $table->index('fatura_id');
            $table->index('catalogo_item_id');
            $table->index('centro_custo_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fatura_itens');
    }
};
