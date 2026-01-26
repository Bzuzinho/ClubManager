<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        Schema::create('patronos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('nome');
            $table->string('nif')->nullable();
            $table->text('morada')->nullable();
            $table->string('contacto')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();

            $table->index('club_id');
            $table->index('nif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patronos');
    }
};
