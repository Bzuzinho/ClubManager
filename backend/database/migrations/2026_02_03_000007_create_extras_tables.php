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
        // Create patrocinadores table (sponsors)
        Schema::create('patrocinadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('nome');
            $table->string('logo')->nullable();
            $table->string('tipo'); // principal, secundario, apoio
            $table->date('contrato_inicio');
            $table->date('contrato_fim')->nullable();
            $table->decimal('valor_anual', 10, 2)->nullable();
            $table->string('contacto_nome')->nullable();
            $table->string('contacto_email')->nullable();
            $table->string('contacto_telefone')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index('club_id');
            $table->index('tipo');
            $table->index('ativo');
            $table->index('contrato_inicio');
        });

        // Add CHECK constraint for tipo
        DB::statement("ALTER TABLE patrocinadores ADD CONSTRAINT patrocinadores_tipo_check CHECK (tipo IN ('principal', 'secundario', 'apoio'))");

        // Create noticias table (news)
        Schema::create('noticias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('titulo');
            $table->text('conteudo');
            $table->string('imagem')->nullable();
            $table->boolean('destaque')->default(false);
            $table->foreignId('autor')->constrained('users')->onDelete('cascade');
            $table->timestamp('data_publicacao');
            $table->json('categorias')->nullable();
            $table->timestamps();

            $table->index('club_id');
            $table->index('autor');
            $table->index('destaque');
            $table->index('data_publicacao');
        });
    }

    public function down(): void
    {
        // Drop CHECK constraint
        DB::statement("ALTER TABLE patrocinadores DROP CONSTRAINT IF EXISTS patrocinadores_tipo_check");

        // Drop tables in reverse order
        Schema::dropIfExists('noticias');
        Schema::dropIfExists('patrocinadores');
    }
};
