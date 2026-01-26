<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('fatura_id')->constrained('faturas')->onDelete('cascade');
            $table->date('data_pagamento');
            $table->decimal('valor', 10, 2);
            $table->string('metodo');
            $table->string('referencia')->nullable();
            $table->foreignId('banco_id')->nullable()->constrained('bancos')->onDelete('set null');
            $table->unsignedBigInteger('ficheiro_comprovativo_id')->nullable();
            $table->timestamps();

            $table->index('club_id');
            $table->index('fatura_id');
            $table->index('data_pagamento');
            $table->index('metodo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
