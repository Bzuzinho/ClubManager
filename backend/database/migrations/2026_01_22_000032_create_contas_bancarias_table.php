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
        Schema::create('contas_bancarias', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // Conta Principal, Conta Eventos, etc
            $table->string('banco');
            $table->string('iban', 25)->unique();
            $table->string('swift')->nullable();
            $table->decimal('saldo_inicial', 10, 2)->default(0);
            $table->decimal('saldo_atual', 10, 2)->default(0);
            $table->boolean('ativa')->default(true);
            $table->boolean('principal')->default(false); // Conta principal do clube
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            $table->index('iban');
            $table->index('ativa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contas_bancarias');
    }
};
