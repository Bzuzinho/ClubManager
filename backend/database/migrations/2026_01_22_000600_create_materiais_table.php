<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('materiais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('artigo_id')->nullable()->constrained('artigos')->onDelete('set null');
            $table->string('designacao')->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('preco', 10, 2)->nullable();
            $table->date('garantia_ate')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->index('club_id');
            $table->index('artigo_id');
            $table->index('stock');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materiais');
    }
};
