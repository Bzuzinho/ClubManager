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
        // Create artigos_loja table
        Schema::create('artigos_loja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->string('categoria');
            $table->decimal('preco_venda', 10, 2);
            $table->decimal('preco_custo', 10, 2)->nullable();
            $table->integer('stock_atual')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->foreignId('fornecedor_id')->nullable()->constrained('fornecedores')->onDelete('set null');
            $table->foreignId('centro_custo_id')->nullable()->constrained('centros_custo')->onDelete('set null');
            $table->string('imagem')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index('club_id');
            $table->index('categoria');
            $table->index('fornecedor_id');
            $table->index('ativo');
        });

        // Adjust movimentos_stock table (if exists) or create it WITHOUT encomenda_id FK (will add later)
        if (!Schema::hasTable('movimentos_stock')) {
            Schema::create('movimentos_stock', function (Blueprint $table) {
                $table->id();
                $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
                $table->foreignId('artigo_id')->constrained('artigos_loja')->onDelete('cascade');
                $table->string('tipo'); // entrada, saida, ajuste, devolucao
                $table->integer('quantidade');
                $table->integer('stock_anterior');
                $table->integer('stock_novo');
                $table->text('motivo')->nullable();
                $table->foreignId('fornecedor_id')->nullable()->constrained('fornecedores')->onDelete('set null');
                $table->unsignedBigInteger('encomenda_id')->nullable(); // FK will be added after encomendas_artigos is created
                $table->decimal('valor_unitario', 10, 2)->nullable();
                $table->foreignId('centro_custo_id')->nullable()->constrained('centros_custo')->onDelete('set null');
                $table->foreignId('registado_por')->nullable()->constrained('users')->onDelete('set null');
                $table->date('data_movimento');
                $table->timestamps();

                $table->index('club_id');
                $table->index('artigo_id');
                $table->index('tipo');
                $table->index('data_movimento');
                $table->index('encomenda_id');
            });
        } else {
            Schema::table('movimentos_stock', function (Blueprint $table) {
                if (!Schema::hasColumn('movimentos_stock', 'artigo_id')) {
                    $table->foreignId('artigo_id')->nullable()->constrained('artigos_loja')->onDelete('cascade')->after('club_id');
                }
                if (!Schema::hasColumn('movimentos_stock', 'tipo')) {
                    $table->string('tipo')->nullable()->after('artigo_id');
                }
                if (!Schema::hasColumn('movimentos_stock', 'stock_anterior')) {
                    $table->integer('stock_anterior')->default(0)->after('quantidade');
                }
                if (!Schema::hasColumn('movimentos_stock', 'stock_novo')) {
                    $table->integer('stock_novo')->default(0)->after('stock_anterior');
                }
                if (!Schema::hasColumn('movimentos_stock', 'fornecedor_id')) {
                    $table->foreignId('fornecedor_id')->nullable()->constrained('fornecedores')->onDelete('set null')->after('motivo');
                }
                if (!Schema::hasColumn('movimentos_stock', 'valor_unitario')) {
                    $table->decimal('valor_unitario', 10, 2)->nullable()->after('encomenda_id');
                }
                if (!Schema::hasColumn('movimentos_stock', 'data_movimento')) {
                    $table->date('data_movimento')->nullable()->after('registado_por');
                }
            });
        }

        // Add CHECK constraint for tipo in movimentos_stock
        DB::statement("ALTER TABLE movimentos_stock ADD CONSTRAINT IF NOT EXISTS movimentos_stock_tipo_check CHECK (tipo IN ('entrada', 'saida', 'ajuste', 'devolucao'))");

        // Create encomendas_artigos table
        Schema::create('encomendas_artigos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->date('data_encomenda');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('artigo_id')->constrained('artigos_loja')->onDelete('cascade');
            $table->integer('quantidade');
            $table->decimal('valor_unitario', 10, 2);
            $table->decimal('valor_total', 10, 2);
            $table->foreignId('escalao_id')->nullable()->constrained('escaloes')->onDelete('set null');
            $table->foreignId('centro_custo_id')->nullable()->constrained('centros_custo')->onDelete('set null');
            $table->string('local_entrega'); // clube, morada_atleta, outro
            $table->text('morada_entrega')->nullable();
            $table->string('estado'); // pendente, aprovada, em_preparacao, entregue, cancelada
            $table->date('data_entrega')->nullable();
            $table->text('observacoes')->nullable();
            $table->foreignId('fatura_id')->nullable()->constrained('faturas')->onDelete('set null');
            $table->foreignId('movimento_id')->nullable()->constrained('movimentos')->onDelete('set null');
            $table->foreignId('criado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('club_id');
            $table->index('user_id');
            $table->index('artigo_id');
            $table->index('estado');
            $table->index('data_encomenda');
        });

        // Add CHECK constraints for encomendas_artigos
        DB::statement("ALTER TABLE encomendas_artigos ADD CONSTRAINT encomendas_artigos_local_check CHECK (local_entrega IN ('clube', 'morada_atleta', 'outro'))");
        DB::statement("ALTER TABLE encomendas_artigos ADD CONSTRAINT encomendas_artigos_estado_check CHECK (estado IN ('pendente', 'aprovada', 'em_preparacao', 'entregue', 'cancelada'))");

        // Create produtos table (catalog-like, distinct from artigos_loja)
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->string('imagem')->nullable();
            $table->string('categoria');
            $table->decimal('preco', 10, 2);
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index('club_id');
            $table->index('categoria');
            $table->index('ativo');
        });

        // Create vendas table
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('produto_id')->constrained('produtos')->onDelete('cascade');
            $table->integer('quantidade');
            $table->decimal('preco_unitario', 10, 2);
            $table->decimal('total', 10, 2);
            $table->foreignId('cliente_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('vendedor_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('data');
            $table->string('metodo_pagamento'); // dinheiro, cartao, mbway, transferencia
            $table->timestamps();

            $table->index('club_id');
            $table->index('produto_id');
            $table->index('cliente_id');
            $table->index('vendedor_id');
            $table->index('data');
        });

        // Add CHECK constraint for metodo_pagamento
        DB::statement("ALTER TABLE vendas ADD CONSTRAINT vendas_metodo_check CHECK (metodo_pagamento IN ('dinheiro', 'cartao', 'mbway', 'transferencia'))");

        // Now add the FK constraint for movimentos_stock.encomenda_id (after encomendas_artigos exists)
        if (Schema::hasTable('movimentos_stock') && Schema::hasColumn('movimentos_stock', 'encomenda_id')) {
            Schema::table('movimentos_stock', function (Blueprint $table) {
                $table->foreign('encomenda_id')->references('id')->on('encomendas_artigos')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        // Drop FK constraint first if it exists
        if (Schema::hasTable('movimentos_stock')) {
            Schema::table('movimentos_stock', function (Blueprint $table) {
                $table->dropForeign(['encomenda_id']);
            });
        }

        // Drop CHECK constraints
        DB::statement("ALTER TABLE vendas DROP CONSTRAINT IF EXISTS vendas_metodo_check");
        DB::statement("ALTER TABLE encomendas_artigos DROP CONSTRAINT IF EXISTS encomendas_artigos_local_check");
        DB::statement("ALTER TABLE encomendas_artigos DROP CONSTRAINT IF EXISTS encomendas_artigos_estado_check");
        DB::statement("ALTER TABLE movimentos_stock DROP CONSTRAINT IF EXISTS movimentos_stock_tipo_check");

        // Drop tables in reverse order
        Schema::dropIfExists('vendas');
        Schema::dropIfExists('produtos');
        Schema::dropIfExists('encomendas_artigos');
        
        // Only drop movimentos_stock if it was created by this migration
        // Otherwise, remove added columns
        if (Schema::hasTable('movimentos_stock')) {
            $checkIfCreated = Schema::hasColumn('movimentos_stock', 'artigo_id');
            if ($checkIfCreated) {
                Schema::dropIfExists('movimentos_stock');
            }
        }
        
        Schema::dropIfExists('artigos_loja');
    }
};
