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
        Schema::create('tipos_documento', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique(); // CC/BI, Atestado Médico, Autorização RGPD, etc
            $table->string('codigo')->unique(); // CC, ATESTADO, RGPD, etc
            $table->text('descricao')->nullable();
            $table->boolean('obrigatorio')->default(false); // Se é obrigatório para todos
            $table->boolean('tem_validade')->default(false); // Se expira
            $table->integer('validade_meses')->nullable(); // Meses de validade (se aplicável)
            $table->string('aplicavel_a')->nullable(); // atleta, membro, encarregado, staff
            $table->boolean('ativo')->default(true);
            $table->integer('ordem')->default(0);
            $table->timestamps();
            
            $table->index('codigo');
            $table->index('obrigatorio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_documento');
    }
};
