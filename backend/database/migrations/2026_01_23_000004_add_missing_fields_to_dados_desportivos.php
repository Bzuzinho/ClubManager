<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    
    public function up(): void
    {
        Schema::table('dados_desportivos', function (Blueprint $table) {
            $table->string('cartao_federacao')->nullable()->after('num_federacao');
            $table->string('inscricao')->nullable()->after('data_inscricao');
            $table->boolean('ativo')->default(true)->after('informacoes_medicas');
        });
    }

    public function down(): void
    {
        Schema::table('dados_desportivos', function (Blueprint $table) {
            $table->dropColumn(['cartao_federacao', 'inscricao', 'ativo']);
        });
    }
};
