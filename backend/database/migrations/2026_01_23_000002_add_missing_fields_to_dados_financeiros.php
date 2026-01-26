<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    
    public function up(): void
    {
        Schema::table('dados_financeiros', function (Blueprint $table) {
            $table->string('conta_corrente')->nullable()->after('mensalidade_id');
        });
    }

    public function down(): void
    {
        Schema::table('dados_financeiros', function (Blueprint $table) {
            $table->dropColumn('conta_corrente');
        });
    }
};
