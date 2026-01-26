<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    
    public function up(): void
    {
        Schema::table('dados_configuracao', function (Blueprint $table) {
            $table->boolean('rgpd_assinado')->default(false)->after('rgpd');
            $table->string('arquivo_rgpd')->nullable()->after('data_rgpd');
            $table->string('arquivo_consentimento')->nullable()->after('data_consentimento');
            $table->string('arquivo_afiliacao')->nullable()->after('data_afiliacao');
            $table->string('declaracao_transporte_arquivo')->nullable()->after('declaracao_transporte');
        });
    }

    public function down(): void
    {
        Schema::table('dados_configuracao', function (Blueprint $table) {
            $table->dropColumn([
                'rgpd_assinado',
                'arquivo_rgpd',
                'arquivo_consentimento',
                'arquivo_afiliacao',
                'declaracao_transporte_arquivo'
            ]);
        });
    }
};
