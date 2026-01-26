<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    
    public function up(): void
    {
        Schema::table('dados_pessoais', function (Blueprint $table) {
            $table->string('foto_perfil')->nullable()->after('user_id');
            $table->string('estado_civil')->nullable()->after('nacionalidade');
            $table->string('ocupacao')->nullable()->after('estado_civil');
            $table->string('empresa')->nullable()->after('ocupacao');
            $table->string('escola')->nullable()->after('empresa');
            $table->boolean('menor')->default(false)->after('sexo');
            $table->integer('numero_irmaos')->nullable()->after('menor');
        });
    }

    public function down(): void
    {
        Schema::table('dados_pessoais', function (Blueprint $table) {
            $table->dropColumn([
                'foto_perfil',
                'estado_civil',
                'ocupacao',
                'empresa',
                'escola',
                'menor',
                'numero_irmaos'
            ]);
        });
    }
};
