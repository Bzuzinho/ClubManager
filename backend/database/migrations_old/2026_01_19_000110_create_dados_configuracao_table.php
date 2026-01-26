<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dados_configuracao', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            // RGPD / legais
            $table->boolean('consentimento_rgpd')->default(false);
            $table->timestamp('consentimento_rgpd_em')->nullable();

            $table->boolean('declaracao_transporte')->default(false);
            $table->timestamp('declaracao_transporte_em')->nullable();

            // Afiliação (ajustável)
            $table->boolean('afiliado')->default(false);
            $table->string('numero_afiliacao')->nullable();
            $table->date('afiliacao_validade')->nullable();

            // Operacional: registo de envio de acessos
            $table->timestamp('acessos_enviados_em')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dados_configuracao');
    }
};
