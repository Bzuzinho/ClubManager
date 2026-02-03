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
        // Adjust eventos_tipos table
        Schema::table('eventos_tipos', function (Blueprint $table) {
            if (!Schema::hasColumn('eventos_tipos', 'gera_taxa')) {
                $table->boolean('gera_taxa')->default(false)->after('ativo');
            }
            if (!Schema::hasColumn('eventos_tipos', 'requer_convocatoria')) {
                $table->boolean('requer_convocatoria')->default(false)->after('gera_taxa');
            }
            if (!Schema::hasColumn('eventos_tipos', 'requer_transporte')) {
                $table->boolean('requer_transporte')->default(false)->after('requer_convocatoria');
            }
            if (!Schema::hasColumn('eventos_tipos', 'visibilidade_default')) {
                $table->string('visibilidade_default')->default('publico')->after('requer_transporte');
            }
        });

        // Add CHECK constraint for visibilidade_default
        DB::statement("ALTER TABLE eventos_tipos ADD CONSTRAINT IF NOT EXISTS eventos_tipos_visibilidade_check CHECK (visibilidade_default IN ('privado', 'restrito', 'publico'))");

        // Adjust eventos table with all Spark fields
        Schema::table('eventos', function (Blueprint $table) {
            if (!Schema::hasColumn('eventos', 'hora_inicio')) {
                $table->time('hora_inicio')->nullable()->after('data_inicio');
            }
            // Note: data_fim already exists as dateTime, so we don't add it again
            if (!Schema::hasColumn('eventos', 'hora_fim')) {
                $table->time('hora_fim')->nullable()->after('data_fim');
            }
            if (!Schema::hasColumn('eventos', 'local_detalhes')) {
                $table->text('local_detalhes')->nullable()->after('local');
            }
            if (!Schema::hasColumn('eventos', 'tipo_piscina')) {
                $table->string('tipo_piscina')->nullable()->after('tipo_id');
            }
            if (!Schema::hasColumn('eventos', 'visibilidade')) {
                $table->string('visibilidade')->default('publico')->after('tipo_piscina');
            }
            if (!Schema::hasColumn('eventos', 'escaloes_elegiveis')) {
                $table->json('escaloes_elegiveis')->nullable()->after('visibilidade');
            }
            if (!Schema::hasColumn('eventos', 'transporte_necessario')) {
                $table->boolean('transporte_necessario')->default(false)->after('escaloes_elegiveis');
            }
            if (!Schema::hasColumn('eventos', 'transporte_detalhes')) {
                $table->text('transporte_detalhes')->nullable()->after('transporte_necessario');
            }
            if (!Schema::hasColumn('eventos', 'hora_partida')) {
                $table->time('hora_partida')->nullable()->after('transporte_detalhes');
            }
            if (!Schema::hasColumn('eventos', 'local_partida')) {
                $table->string('local_partida')->nullable()->after('hora_partida');
            }
            if (!Schema::hasColumn('eventos', 'taxa_inscricao')) {
                $table->decimal('taxa_inscricao', 10, 2)->nullable()->after('local_partida');
            }
            if (!Schema::hasColumn('eventos', 'custo_inscricao_por_prova')) {
                $table->decimal('custo_inscricao_por_prova', 10, 2)->nullable()->after('taxa_inscricao');
            }
            if (!Schema::hasColumn('eventos', 'custo_inscricao_por_salto')) {
                $table->decimal('custo_inscricao_por_salto', 10, 2)->nullable()->after('custo_inscricao_por_prova');
            }
            if (!Schema::hasColumn('eventos', 'custo_inscricao_estafeta')) {
                $table->decimal('custo_inscricao_estafeta', 10, 2)->nullable()->after('custo_inscricao_por_salto');
            }
            if (!Schema::hasColumn('eventos', 'observacoes')) {
                $table->text('observacoes')->nullable()->after('centro_custo_id');
            }
            if (!Schema::hasColumn('eventos', 'convocatoria_ficheiro')) {
                $table->string('convocatoria_ficheiro')->nullable()->after('observacoes');
            }
            if (!Schema::hasColumn('eventos', 'regulamento_ficheiro')) {
                $table->string('regulamento_ficheiro')->nullable()->after('convocatoria_ficheiro');
            }
            if (!Schema::hasColumn('eventos', 'criado_por')) {
                $table->foreignId('criado_por')->nullable()->constrained('users')->onDelete('set null')->after('regulamento_ficheiro');
            }
            if (!Schema::hasColumn('eventos', 'recorrente')) {
                $table->boolean('recorrente')->default(false)->after('estado');
            }
            if (!Schema::hasColumn('eventos', 'recorrencia_data_inicio')) {
                $table->date('recorrencia_data_inicio')->nullable()->after('recorrente');
            }
            if (!Schema::hasColumn('eventos', 'recorrencia_data_fim')) {
                $table->date('recorrencia_data_fim')->nullable()->after('recorrencia_data_inicio');
            }
            if (!Schema::hasColumn('eventos', 'recorrencia_dias_semana')) {
                $table->json('recorrencia_dias_semana')->nullable()->after('recorrencia_data_fim');
            }
            if (!Schema::hasColumn('eventos', 'evento_pai_id')) {
                $table->foreignId('evento_pai_id')->nullable()->constrained('eventos')->onDelete('cascade')->after('recorrencia_dias_semana');
            }
        });

        // Add CHECK constraints for eventos
        DB::statement("ALTER TABLE eventos ADD CONSTRAINT IF NOT EXISTS eventos_tipo_piscina_check CHECK (tipo_piscina IN ('piscina_25m', 'piscina_50m', 'aguas_abertas'))");
        DB::statement("ALTER TABLE eventos ADD CONSTRAINT IF NOT EXISTS eventos_visibilidade_check CHECK (visibilidade IN ('privado', 'restrito', 'publico'))");
        DB::statement("ALTER TABLE eventos ADD CONSTRAINT IF NOT EXISTS eventos_estado_check CHECK (estado IN ('rascunho', 'agendado', 'em_curso', 'concluido', 'cancelado'))");

        // Create convocatorias_grupos table
        Schema::create('convocatorias_grupos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->foreignId('criado_por')->constrained('users')->onDelete('cascade');
            $table->time('hora_encontro')->nullable();
            $table->string('local_encontro')->nullable();
            $table->text('observacoes')->nullable();
            $table->string('tipo_custo'); // por_salto, por_atleta
            $table->decimal('valor_por_salto', 10, 2)->nullable();
            $table->decimal('valor_por_estafeta', 10, 2)->nullable();
            $table->decimal('valor_inscricao_unitaria', 10, 2)->nullable();
            $table->decimal('valor_inscricao_calculado', 10, 2)->nullable();
            $table->foreignId('movimento_id')->nullable()->constrained('movimentos')->onDelete('set null');
            $table->timestamps();

            $table->index('club_id');
            $table->index('evento_id');
            $table->index('criado_por');
        });

        // Add CHECK constraint for tipo_custo
        DB::statement("ALTER TABLE convocatorias_grupos ADD CONSTRAINT convocatorias_grupos_tipo_custo_check CHECK (tipo_custo IN ('por_salto', 'por_atleta'))");

        // Create convocatorias_atletas table
        Schema::create('convocatorias_atletas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('convocatoria_grupo_id')->constrained('convocatorias_grupos')->onDelete('cascade');
            $table->foreignId('atleta_id')->constrained('users')->onDelete('cascade');
            $table->json('provas')->nullable();
            $table->boolean('presente')->default(false);
            $table->boolean('confirmado')->default(false);
            $table->timestamps();

            $table->unique(['convocatoria_grupo_id', 'atleta_id']);
            $table->index('convocatoria_grupo_id');
            $table->index('atleta_id');
        });

        // Create eventos_convocatorias table
        Schema::create('eventos_convocatorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('data_convocatoria');
            $table->string('estado_confirmacao')->default('pendente'); // pendente, confirmado, recusado
            $table->timestamp('data_resposta')->nullable();
            $table->text('justificacao')->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('transporte_clube')->default(false);
            $table->timestamps();

            $table->unique(['evento_id', 'user_id']);
            $table->index('club_id');
            $table->index('evento_id');
            $table->index('user_id');
            $table->index('estado_confirmacao');
        });

        // Add CHECK constraint for estado_confirmacao
        DB::statement("ALTER TABLE eventos_convocatorias ADD CONSTRAINT eventos_convocatorias_estado_check CHECK (estado_confirmacao IN ('pendente', 'confirmado', 'recusado'))");

        // Create eventos_presencas table
        Schema::create('eventos_presencas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('estado'); // presente, ausente, justificado
            $table->time('hora_chegada')->nullable();
            $table->text('observacoes')->nullable();
            $table->foreignId('registado_por')->constrained('users')->onDelete('cascade');
            $table->timestamp('registado_em');
            $table->timestamps();

            $table->unique(['evento_id', 'user_id']);
            $table->index('club_id');
            $table->index('evento_id');
            $table->index('user_id');
            $table->index('estado');
        });

        // Add CHECK constraint for estado in eventos_presencas
        DB::statement("ALTER TABLE eventos_presencas ADD CONSTRAINT eventos_presencas_estado_check CHECK (estado IN ('presente', 'ausente', 'justificado'))");

        // Create eventos_resultados table
        Schema::create('eventos_resultados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('prova');
            $table->string('tempo')->nullable();
            $table->integer('classificacao')->nullable();
            $table->string('piscina')->nullable();
            $table->string('escalao')->nullable();
            $table->text('observacoes')->nullable();
            $table->string('epoca')->nullable();
            $table->foreignId('registado_por')->constrained('users')->onDelete('cascade');
            $table->timestamp('registado_em');
            $table->timestamps();

            $table->index('club_id');
            $table->index('evento_id');
            $table->index('user_id');
            $table->index('prova');
        });
    }

    public function down(): void
    {
        // Drop tables in reverse order
        Schema::dropIfExists('eventos_resultados');
        Schema::dropIfExists('eventos_presencas');
        Schema::dropIfExists('eventos_convocatorias');
        Schema::dropIfExists('convocatorias_atletas');
        Schema::dropIfExists('convocatorias_grupos');

        // Drop CHECK constraints
        DB::statement("ALTER TABLE eventos DROP CONSTRAINT IF EXISTS eventos_tipo_piscina_check");
        DB::statement("ALTER TABLE eventos DROP CONSTRAINT IF EXISTS eventos_visibilidade_check");
        DB::statement("ALTER TABLE eventos DROP CONSTRAINT IF EXISTS eventos_estado_check");
        DB::statement("ALTER TABLE eventos_tipos DROP CONSTRAINT IF EXISTS eventos_tipos_visibilidade_check");

        // Remove fields from eventos (only if they were added by this migration)
        Schema::table('eventos', function (Blueprint $table) {
            $columns = [
                'hora_inicio', 'hora_fim', 'local_detalhes', 'tipo_piscina',
                'visibilidade', 'escaloes_elegiveis', 'transporte_necessario', 'transporte_detalhes',
                'hora_partida', 'local_partida', 'taxa_inscricao', 'custo_inscricao_por_prova',
                'custo_inscricao_por_salto', 'custo_inscricao_estafeta', 'observacoes',
                'convocatoria_ficheiro', 'regulamento_ficheiro', 'criado_por', 'recorrente',
                'recorrencia_data_inicio', 'recorrencia_data_fim', 'recorrencia_dias_semana', 'evento_pai_id'
            ];
            // Note: data_fim is NOT removed as it existed before this migration
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('eventos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // Remove fields from eventos_tipos (only if they were added by this migration)
        Schema::table('eventos_tipos', function (Blueprint $table) {
            $columns = ['gera_taxa', 'requer_convocatoria', 'requer_transporte', 'visibilidade_default'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('eventos_tipos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
