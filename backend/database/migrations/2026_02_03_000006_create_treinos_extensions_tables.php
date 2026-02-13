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
        // Create treinos_series table
        Schema::create('treinos_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('treino_id')->constrained('treinos')->onDelete('cascade');
            $table->integer('ordem');
            $table->text('descricao_texto');
            $table->integer('distancia_total_m');
            $table->string('zona_intensidade')->nullable(); // Z1, Z2, Z3, Z4, Z5
            $table->string('estilo')->nullable(); // crawl, costas, brucos, mariposa, estilos, livres
            $table->integer('repeticoes')->nullable();
            $table->string('intervalo')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index('club_id');
            $table->index('treino_id');
            $table->index('ordem');
        });

        // Add CHECK constraints for treinos_series
        DB::statement("ALTER TABLE treinos_series ADD CONSTRAINT treinos_series_zona_check CHECK (zona_intensidade IN ('Z1', 'Z2', 'Z3', 'Z4', 'Z5'))");
        DB::statement("ALTER TABLE treinos_series ADD CONSTRAINT treinos_series_estilo_check CHECK (estilo IN ('crawl', 'costas', 'brucos', 'mariposa', 'estilos', 'livres'))");

        // Create treinos_atletas table
        Schema::create('treinos_atletas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('treino_id')->constrained('treinos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('presente')->default(false);
            $table->string('estado')->nullable(); // presente, ausente, justificado
            $table->integer('volume_real_m')->nullable();
            $table->integer('rpe')->nullable(); // Rating of Perceived Exertion 1-10
            $table->text('observacoes_tecnicas')->nullable();
            $table->foreignId('registado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('registado_em')->nullable();
            $table->timestamps();

            $table->unique(['treino_id', 'user_id']);
            $table->index('club_id');
            $table->index('treino_id');
            $table->index('user_id');
            $table->index('presente');
        });

        // Add CHECK constraints for treinos_atletas
        DB::statement("ALTER TABLE treinos_atletas ADD CONSTRAINT treinos_atletas_estado_check CHECK (estado IN ('presente', 'ausente', 'justificado'))");
        DB::statement("ALTER TABLE treinos_atletas ADD CONSTRAINT treinos_atletas_rpe_check CHECK (rpe >= 1 AND rpe <= 10)");
    }

    public function down(): void
    {
        // Drop CHECK constraints
        DB::statement("ALTER TABLE treinos_atletas DROP CONSTRAINT IF EXISTS treinos_atletas_estado_check");
        DB::statement("ALTER TABLE treinos_atletas DROP CONSTRAINT IF EXISTS treinos_atletas_rpe_check");
        DB::statement("ALTER TABLE treinos_series DROP CONSTRAINT IF EXISTS treinos_series_zona_check");
        DB::statement("ALTER TABLE treinos_series DROP CONSTRAINT IF EXISTS treinos_series_estilo_check");

        // Drop tables in reverse order
        Schema::dropIfExists('treinos_atletas');
        Schema::dropIfExists('treinos_series');
    }
};
