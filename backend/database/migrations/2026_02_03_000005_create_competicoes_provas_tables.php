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
        // Create competicoes table
        Schema::create('competicoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('nome');
            $table->string('local');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->string('tipo'); // oficial, interna, masters, formacao, outro
            $table->foreignId('evento_id')->nullable()->constrained('eventos')->onDelete('set null');
            $table->timestamps();

            $table->index('club_id');
            $table->index('tipo');
            $table->index('data_inicio');
            $table->index('evento_id');
        });

        // Add CHECK constraint for tipo
        DB::statement("ALTER TABLE competicoes ADD CONSTRAINT competicoes_tipo_check CHECK (tipo IN ('oficial', 'interna', 'masters', 'formacao', 'outro'))");

        // Check if provas table exists (from TypeScript types, it's used differently)
        if (!Schema::hasTable('provas')) {
            Schema::create('provas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
                $table->foreignId('competicao_id')->constrained('competicoes')->onDelete('cascade');
                $table->string('estilo'); // crawl, costas, brucos, mariposa, estilos, livres
                $table->integer('distancia_m');
                $table->string('genero'); // masculino, feminino, misto
                $table->foreignId('escalao_id')->nullable()->constrained('escaloes')->onDelete('set null');
                $table->integer('ordem_prova')->nullable();
                $table->timestamps();

                $table->index('club_id');
                $table->index('competicao_id');
                $table->index('estilo');
                $table->index('genero');
            });
        } else {
            // Adjust existing provas table if needed
            Schema::table('provas', function (Blueprint $table) {
                if (!Schema::hasColumn('provas', 'competicao_id')) {
                    $table->foreignId('competicao_id')->nullable()->constrained('competicoes')->onDelete('cascade')->after('club_id');
                }
                if (!Schema::hasColumn('provas', 'estilo')) {
                    $table->string('estilo')->nullable()->after('competicao_id');
                }
                if (!Schema::hasColumn('provas', 'distancia_m')) {
                    $table->integer('distancia_m')->nullable()->after('estilo');
                }
                if (!Schema::hasColumn('provas', 'genero')) {
                    $table->string('genero')->nullable()->after('distancia_m');
                }
                if (!Schema::hasColumn('provas', 'ordem_prova')) {
                    $table->integer('ordem_prova')->nullable()->after('escalao_id');
                }
            });
        }

        // Add CHECK constraints for provas
        DB::statement("ALTER TABLE provas ADD CONSTRAINT IF NOT EXISTS provas_estilo_check CHECK (estilo IN ('crawl', 'costas', 'brucos', 'mariposa', 'estilos', 'livres'))");
        DB::statement("ALTER TABLE provas ADD CONSTRAINT IF NOT EXISTS provas_genero_check CHECK (genero IN ('masculino', 'feminino', 'misto'))");

        // Create inscricoes_provas table
        Schema::create('inscricoes_provas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('prova_id')->constrained('provas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('estado'); // inscrito, confirmado, desistiu
            $table->decimal('valor_inscricao', 10, 2)->nullable();
            $table->foreignId('fatura_id')->nullable()->constrained('faturas')->onDelete('set null');
            $table->foreignId('movimento_id')->nullable()->constrained('movimentos')->onDelete('set null');
            $table->timestamps();

            $table->unique(['prova_id', 'user_id']);
            $table->index('club_id');
            $table->index('prova_id');
            $table->index('user_id');
            $table->index('estado');
        });

        // Add CHECK constraint for estado
        DB::statement("ALTER TABLE inscricoes_provas ADD CONSTRAINT inscricoes_provas_estado_check CHECK (estado IN ('inscrito', 'confirmado', 'desistiu'))");

        // Check if resultados table exists and adjust or create
        if (!Schema::hasTable('resultados')) {
            Schema::create('resultados', function (Blueprint $table) {
                $table->id();
                $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
                $table->foreignId('prova_id')->constrained('provas')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->integer('tempo_oficial'); // in milliseconds
                $table->integer('posicao')->nullable();
                $table->integer('pontos_fina')->nullable();
                $table->boolean('desclassificado')->default(false);
                $table->text('observacoes')->nullable();
                $table->timestamps();

                $table->index('club_id');
                $table->index('prova_id');
                $table->index('user_id');
                $table->index('posicao');
            });
        } else {
            Schema::table('resultados', function (Blueprint $table) {
                if (!Schema::hasColumn('resultados', 'prova_id')) {
                    $table->foreignId('prova_id')->nullable()->constrained('provas')->onDelete('cascade')->after('club_id');
                }
                if (!Schema::hasColumn('resultados', 'tempo_oficial')) {
                    $table->integer('tempo_oficial')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('resultados', 'posicao')) {
                    $table->integer('posicao')->nullable()->after('tempo_oficial');
                }
                if (!Schema::hasColumn('resultados', 'pontos_fina')) {
                    $table->integer('pontos_fina')->nullable()->after('posicao');
                }
                if (!Schema::hasColumn('resultados', 'desclassificado')) {
                    $table->boolean('desclassificado')->default(false)->after('pontos_fina');
                }
                if (!Schema::hasColumn('resultados', 'observacoes')) {
                    $table->text('observacoes')->nullable()->after('desclassificado');
                }
            });
        }

        // Create resultados_splits table
        Schema::create('resultados_splits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resultado_id')->constrained('resultados')->onDelete('cascade');
            $table->integer('distancia_parcial_m');
            $table->integer('tempo_parcial'); // in milliseconds
            $table->timestamps();

            $table->index('resultado_id');
        });
    }

    public function down(): void
    {
        // Drop tables in reverse order
        Schema::dropIfExists('resultados_splits');
        
        // Drop CHECK constraints
        DB::statement("ALTER TABLE inscricoes_provas DROP CONSTRAINT IF EXISTS inscricoes_provas_estado_check");
        DB::statement("ALTER TABLE provas DROP CONSTRAINT IF EXISTS provas_estilo_check");
        DB::statement("ALTER TABLE provas DROP CONSTRAINT IF EXISTS provas_genero_check");
        DB::statement("ALTER TABLE competicoes DROP CONSTRAINT IF EXISTS competicoes_tipo_check");
        
        Schema::dropIfExists('inscricoes_provas');
        
        // Only drop resultados if it was created by this migration
        $wasCreatedHere = !Schema::hasColumn('resultados', 'evento_id');
        if ($wasCreatedHere) {
            Schema::dropIfExists('resultados');
        } else {
            // Remove fields we added
            Schema::table('resultados', function (Blueprint $table) {
                $columns = ['prova_id', 'tempo_oficial', 'posicao', 'pontos_fina', 'desclassificado', 'observacoes'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('resultados', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
        
        // Only drop provas if it was created by this migration
        // Check if it has the 'competicao_id' which we added
        if (Schema::hasTable('provas')) {
            $wasCreatedHere = Schema::hasColumn('provas', 'competicao_id') && Schema::hasColumn('provas', 'estilo');
            if ($wasCreatedHere && !Schema::hasColumn('provas', 'tipo')) {
                Schema::dropIfExists('provas');
            }
        }
        
        Schema::dropIfExists('competicoes');
    }
};
