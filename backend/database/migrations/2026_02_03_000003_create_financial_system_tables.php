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
        // Create movimentos table first (needed by convocatorias_grupos FK)
        Schema::create('movimentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nome_manual')->nullable();
            $table->string('nif_manual')->nullable();
            $table->text('morada_manual')->nullable();
            $table->string('classificacao'); // receita, despesa
            $table->date('data_emissao');
            $table->date('data_vencimento');
            $table->decimal('valor_total', 10, 2);
            $table->string('estado_pagamento'); // pendente, pago, vencido, parcial, cancelado
            $table->string('numero_recibo')->nullable();
            $table->string('referencia_pagamento')->nullable();
            $table->foreignId('centro_custo_id')->nullable()->constrained('centros_custo')->onDelete('set null');
            $table->string('tipo'); // inscricao, material, servico, outro
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index('club_id');
            $table->index('user_id');
            $table->index('classificacao');
            $table->index('data_emissao');
            $table->index('estado_pagamento');
        });

        // Add CHECK constraints for movimentos
        DB::statement("ALTER TABLE movimentos ADD CONSTRAINT movimentos_classificacao_check CHECK (classificacao IN ('receita', 'despesa'))");
        DB::statement("ALTER TABLE movimentos ADD CONSTRAINT movimentos_estado_check CHECK (estado_pagamento IN ('pendente', 'pago', 'vencido', 'parcial', 'cancelado'))");
        DB::statement("ALTER TABLE movimentos ADD CONSTRAINT movimentos_tipo_check CHECK (tipo IN ('inscricao', 'material', 'servico', 'outro'))");

        // Adjust faturas table with Spark fields
        Schema::table('faturas', function (Blueprint $table) {
            if (!Schema::hasColumn('faturas', 'data_fatura')) {
                $table->date('data_fatura')->after('membro_id');
            }
            if (!Schema::hasColumn('faturas', 'data_vencimento')) {
                $table->date('data_vencimento')->nullable()->after('data_emissao');
            }
            if (!Schema::hasColumn('faturas', 'estado_pagamento')) {
                $table->string('estado_pagamento')->default('pendente')->after('valor_total');
            }
            if (!Schema::hasColumn('faturas', 'tipo')) {
                $table->string('tipo')->default('mensalidade')->after('centro_custo_id');
            }
            if (!Schema::hasColumn('faturas', 'observacoes')) {
                $table->text('observacoes')->nullable()->after('tipo');
            }
        });

        // Add CHECK constraints for faturas
        DB::statement("ALTER TABLE faturas ADD CONSTRAINT IF NOT EXISTS faturas_estado_check CHECK (estado_pagamento IN ('pendente', 'pago', 'vencido', 'parcial', 'cancelado'))");
        DB::statement("ALTER TABLE faturas ADD CONSTRAINT IF NOT EXISTS faturas_tipo_check CHECK (tipo IN ('mensalidade', 'inscricao', 'material', 'servico', 'outro'))");

        // Create faturas_itens table
        Schema::create('faturas_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('fatura_id')->constrained('faturas')->onDelete('cascade');
            $table->string('descricao');
            $table->decimal('valor_unitario', 10, 2);
            $table->integer('quantidade')->default(1);
            $table->decimal('imposto_percentual', 5, 2)->default(0);
            $table->decimal('total_linha', 10, 2);
            $table->foreignId('produto_id')->nullable()->constrained('artigos')->onDelete('set null');
            $table->foreignId('centro_custo_id')->nullable()->constrained('centros_custo')->onDelete('set null');
            $table->timestamps();

            $table->index('club_id');
            $table->index('fatura_id');
            $table->index('produto_id');
        });

        // Adjust lancamentos_financeiros table
        if (!Schema::hasTable('lancamentos_financeiros')) {
            Schema::create('lancamentos_financeiros', function (Blueprint $table) {
                $table->id();
                $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
                $table->date('data');
                $table->string('tipo'); // receita, despesa
                $table->string('categoria')->nullable();
                $table->text('descricao');
                $table->decimal('valor', 10, 2);
                $table->foreignId('centro_custo_id')->nullable()->constrained('centros_custo')->onDelete('set null');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('fatura_id')->nullable()->constrained('faturas')->onDelete('set null');
                $table->string('metodo_pagamento')->nullable();
                $table->string('comprovativo')->nullable();
                $table->timestamps();

                $table->index('club_id');
                $table->index('data');
                $table->index('tipo');
                $table->index('categoria');
            });
        } else {
            Schema::table('lancamentos_financeiros', function (Blueprint $table) {
                if (!Schema::hasColumn('lancamentos_financeiros', 'fatura_id')) {
                    $table->foreignId('fatura_id')->nullable()->constrained('faturas')->onDelete('set null')->after('user_id');
                }
                if (!Schema::hasColumn('lancamentos_financeiros', 'metodo_pagamento')) {
                    $table->string('metodo_pagamento')->nullable()->after('fatura_id');
                }
                if (!Schema::hasColumn('lancamentos_financeiros', 'comprovativo')) {
                    $table->string('comprovativo')->nullable()->after('metodo_pagamento');
                }
            });
        }

        // Add CHECK constraint for lancamentos tipo
        DB::statement("ALTER TABLE lancamentos_financeiros ADD CONSTRAINT IF NOT EXISTS lancamentos_financeiros_tipo_check CHECK (tipo IN ('receita', 'despesa'))");

        // Create extratos_bancarios table
        Schema::create('extratos_bancarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('conta')->nullable();
            $table->date('data_movimento');
            $table->text('descricao');
            $table->decimal('valor', 10, 2);
            $table->decimal('saldo', 10, 2)->nullable();
            $table->string('referencia')->nullable();
            $table->foreignId('centro_custo_id')->nullable()->constrained('centros_custo')->onDelete('set null');
            $table->boolean('conciliado')->default(false);
            $table->foreignId('lancamento_id')->nullable()->constrained('lancamentos_financeiros')->onDelete('set null');
            $table->timestamps();

            $table->index('club_id');
            $table->index('data_movimento');
            $table->index('conciliado');
            $table->index('conta');
        });

        // Create movimentos_itens table
        Schema::create('movimentos_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('movimento_id')->constrained('movimentos')->onDelete('cascade');
            $table->string('descricao');
            $table->decimal('valor_unitario', 10, 2);
            $table->integer('quantidade')->default(1);
            $table->decimal('imposto_percentual', 5, 2)->default(0);
            $table->decimal('total_linha', 10, 2);
            $table->foreignId('produto_id')->nullable()->constrained('artigos')->onDelete('set null');
            $table->foreignId('centro_custo_id')->nullable()->constrained('centros_custo')->onDelete('set null');
            $table->foreignId('fatura_id')->nullable()->constrained('faturas')->onDelete('set null');
            $table->timestamps();

            $table->index('club_id');
            $table->index('movimento_id');
            $table->index('produto_id');
        });

        // Create movimentos_convocatorias table
        Schema::create('movimentos_convocatorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('convocatoria_grupo_id')->constrained('convocatorias_grupos')->onDelete('cascade');
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->string('evento_nome');
            $table->string('tipo')->default('convocatoria');
            $table->date('data_emissao');
            $table->decimal('valor', 10, 2);
            $table->timestamps();

            $table->index('club_id');
            $table->index('user_id');
            $table->index('evento_id');
            $table->index('convocatoria_grupo_id');
        });

        // Create movimentos_convocatorias_itens table
        Schema::create('movimentos_convocatorias_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movimento_convocatoria_id')->constrained('movimentos_convocatorias')->onDelete('cascade');
            $table->string('descricao');
            $table->decimal('valor', 10, 2);
            $table->timestamps();

            $table->index('movimento_convocatoria_id', 'idx_mov_conv_itens_mov_conv_id');
        });
    }

    public function down(): void
    {
        // Drop tables in reverse order
        Schema::dropIfExists('movimentos_convocatorias_itens');
        Schema::dropIfExists('movimentos_convocatorias');
        Schema::dropIfExists('movimentos_itens');
        Schema::dropIfExists('extratos_bancarios');
        
        // Drop CHECK constraints
        DB::statement("ALTER TABLE lancamentos_financeiros DROP CONSTRAINT IF EXISTS lancamentos_financeiros_tipo_check");
        
        // Remove fields from lancamentos_financeiros if it existed before
        if (Schema::hasTable('lancamentos_financeiros')) {
            Schema::table('lancamentos_financeiros', function (Blueprint $table) {
                if (Schema::hasColumn('lancamentos_financeiros', 'fatura_id')) {
                    $table->dropColumn('fatura_id');
                }
                if (Schema::hasColumn('lancamentos_financeiros', 'metodo_pagamento')) {
                    $table->dropColumn('metodo_pagamento');
                }
                if (Schema::hasColumn('lancamentos_financeiros', 'comprovativo')) {
                    $table->dropColumn('comprovativo');
                }
            });
        }
        
        Schema::dropIfExists('faturas_itens');
        
        // Drop CHECK constraints for faturas
        DB::statement("ALTER TABLE faturas DROP CONSTRAINT IF EXISTS faturas_estado_check");
        DB::statement("ALTER TABLE faturas DROP CONSTRAINT IF EXISTS faturas_tipo_check");
        
        // Remove fields from faturas
        Schema::table('faturas', function (Blueprint $table) {
            $columns = ['data_fatura', 'data_vencimento', 'estado_pagamento', 'tipo', 'observacoes'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('faturas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
        
        // Drop CHECK constraints for movimentos
        DB::statement("ALTER TABLE movimentos DROP CONSTRAINT IF EXISTS movimentos_classificacao_check");
        DB::statement("ALTER TABLE movimentos DROP CONSTRAINT IF EXISTS movimentos_estado_check");
        DB::statement("ALTER TABLE movimentos DROP CONSTRAINT IF EXISTS movimentos_tipo_check");
        
        Schema::dropIfExists('movimentos');
    }
};
