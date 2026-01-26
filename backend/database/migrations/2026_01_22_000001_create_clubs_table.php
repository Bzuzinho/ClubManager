<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('nome_fiscal');
            $table->string('abreviatura');
            $table->string('nif')->nullable();
            $table->string('morada')->nullable();
            $table->string('contacto_telefonico')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('logo_ficheiro_id')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
