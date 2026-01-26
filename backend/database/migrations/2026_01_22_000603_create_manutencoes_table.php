<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('manutencoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materiais')->onDelete('cascade');
            $table->date('data');
            $table->foreignId('fornecedor_id')->nullable()->constrained('fornecedores')->onDelete('set null');
            $table->decimal('custo', 10, 2)->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index('club_id');
            $table->index('material_id');
            $table->index('data');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manutencoes');
    }
};
