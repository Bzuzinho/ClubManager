<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    public function up(): void
    {
        // Add missing foreign keys
        Schema::table('clubs', function (Blueprint $table) {
            $table->foreign('logo_ficheiro_id')->references('id')->on('ficheiros')->onDelete('set null');
        });

        Schema::table('pagamentos', function (Blueprint $table) {
            $table->foreign('ficheiro_comprovativo_id')->references('id')->on('ficheiros')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->dropForeign(['ficheiro_comprovativo_id']);
        });

        Schema::table('clubs', function (Blueprint $table) {
            $table->dropForeign(['logo_ficheiro_id']);
        });
    }
};
