<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('artigos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('codigo');
            $table->string('nome');
            $table->foreignId('fornecedor_id')->nullable()->constrained('fornecedores')->onDelete('set null');
            $table->foreignId('armazem_id')->nullable()->constrained('armazens')->onDelete('set null');
            $table->foreignId('categoria_id')->nullable()->constrained('categorias_artigos')->onDelete('set null');
            $table->decimal('valor', 10, 2)->default(0);
            $table->decimal('imposto_percent', 5, 2)->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->unique(['club_id', 'codigo']);
            $table->index('club_id');
            $table->index('fornecedor_id');
            $table->index('armazem_id');
            $table->index('categoria_id');
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artigos');
    }
};
