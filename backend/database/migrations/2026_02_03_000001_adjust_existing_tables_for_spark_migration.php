<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        // Add missing fields to dados_pessoais
        Schema::table('dados_pessoais', function (Blueprint $table) {
            $table->string('estado_civil')->nullable()->after('nacionalidade');
            $table->string('ocupacao')->nullable()->after('estado_civil');
            $table->string('empresa')->nullable()->after('ocupacao');
            $table->string('escola')->nullable()->after('empresa');
            $table->integer('numero_irmaos')->nullable()->after('escola');
        });

        // Add CHECK constraint for sexo in dados_pessoais
        DB::statement("ALTER TABLE dados_pessoais ADD CONSTRAINT dados_pessoais_sexo_check CHECK (sexo IN ('masculino', 'feminino'))");
        
        // Add CHECK constraint for estado_civil
        DB::statement("ALTER TABLE dados_pessoais ADD CONSTRAINT dados_pessoais_estado_civil_check CHECK (estado_civil IN ('solteiro', 'casado', 'divorciado', 'viuvo'))");

        // Add missing fields to membros
        Schema::table('membros', function (Blueprint $table) {
            $table->string('foto_perfil')->nullable()->after('numero_socio');
            $table->boolean('menor')->default(false)->after('foto_perfil');
        });

        // Add CHECK constraint for estado in membros
        DB::statement("ALTER TABLE membros ADD CONSTRAINT membros_estado_check CHECK (estado IN ('ativo', 'inativo', 'suspenso'))");

        // Add missing fields to dados_configuracao
        Schema::table('dados_configuracao', function (Blueprint $table) {
            $table->string('arquivo_rgpd')->nullable()->after('data_rgpd');
            $table->string('arquivo_consentimento')->nullable()->after('data_consentimento');
            $table->string('arquivo_afiliacao')->nullable()->after('data_afiliacao');
            $table->string('arquivo_declaracao_transporte')->nullable()->after('declaracao_transporte');
            $table->string('perfil')->default('atleta')->after('email_utilizador');
        });

        // Add CHECK constraint for perfil in dados_configuracao
        DB::statement("ALTER TABLE dados_configuracao ADD CONSTRAINT dados_configuracao_perfil_check CHECK (perfil IN ('admin', 'encarregado', 'atleta', 'staff'))");

        // Add missing fields to dados_financeiros
        Schema::table('dados_financeiros', function (Blueprint $table) {
            $table->decimal('conta_corrente', 10, 2)->default(0)->after('mensalidade_id');
            $table->string('tipo_mensalidade')->nullable()->after('conta_corrente');
        });

        // Add missing fields to dados_desportivos
        Schema::table('dados_desportivos', function (Blueprint $table) {
            $table->string('cartao_federacao')->nullable()->after('num_federacao');
            $table->string('arquivo_inscricao')->nullable()->after('data_inscricao');
            $table->json('arquivo_atestado_medico')->nullable()->after('data_atestado_medico');
            $table->boolean('ativo_desportivo')->default(true)->after('informacoes_medicas');
        });
    }

    public function down(): void
    {
        // Drop CHECK constraints
        DB::statement("ALTER TABLE dados_pessoais DROP CONSTRAINT IF EXISTS dados_pessoais_sexo_check");
        DB::statement("ALTER TABLE dados_pessoais DROP CONSTRAINT IF EXISTS dados_pessoais_estado_civil_check");
        DB::statement("ALTER TABLE membros DROP CONSTRAINT IF EXISTS membros_estado_check");
        DB::statement("ALTER TABLE dados_configuracao DROP CONSTRAINT IF EXISTS dados_configuracao_perfil_check");

        // Remove fields from dados_desportivos
        Schema::table('dados_desportivos', function (Blueprint $table) {
            $table->dropColumn([
                'cartao_federacao',
                'arquivo_inscricao',
                'arquivo_atestado_medico',
                'ativo_desportivo'
            ]);
        });

        // Remove fields from dados_financeiros
        Schema::table('dados_financeiros', function (Blueprint $table) {
            $table->dropColumn(['conta_corrente', 'tipo_mensalidade']);
        });

        // Remove fields from dados_configuracao
        Schema::table('dados_configuracao', function (Blueprint $table) {
            $table->dropColumn([
                'arquivo_rgpd',
                'arquivo_consentimento',
                'arquivo_afiliacao',
                'arquivo_declaracao_transporte',
                'perfil'
            ]);
        });

        // Remove fields from membros
        Schema::table('membros', function (Blueprint $table) {
            $table->dropColumn(['foto_perfil', 'menor']);
        });

        // Remove fields from dados_pessoais
        Schema::table('dados_pessoais', function (Blueprint $table) {
            $table->dropColumn([
                'estado_civil',
                'ocupacao',
                'empresa',
                'escola',
                'numero_irmaos'
            ]);
        });
    }
};
