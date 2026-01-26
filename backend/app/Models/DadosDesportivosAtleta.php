<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DadosDesportivosAtleta extends Model
{
    use HasFactory;

    protected $table = 'dados_desportivos_atleta';

    protected $fillable = [
        'atleta_id',
        'equipa_id',
        'temporada',
        'jogos_realizados',
        'jogos_titular',
        'minutos_jogados',
        'golos',
        'assistencias',
        'cartoes_amarelos',
        'cartoes_vermelhos',
        'treinos_presentes',
        'treinos_totais',
        'percentagem_presenca',
        'media_golos',
        'observacoes',
    ];

    protected $casts = [
        'atleta_id' => 'integer',
        'equipa_id' => 'integer',
        'jogos_realizados' => 'integer',
        'jogos_titular' => 'integer',
        'minutos_jogados' => 'integer',
        'golos' => 'integer',
        'assistencias' => 'integer',
        'cartoes_amarelos' => 'integer',
        'cartoes_vermelhos' => 'integer',
        'treinos_presentes' => 'integer',
        'treinos_totais' => 'integer',
        'percentagem_presenca' => 'decimal:2',
        'media_golos' => 'decimal:2',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function atleta()
    {
        return $this->belongsTo(Atleta::class);
    }

    public function equipa()
    {
        return $this->belongsTo(Equipa::class);
    }
}
