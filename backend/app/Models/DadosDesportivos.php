<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DadosDesportivos extends Model
{
    use HasFactory;

    protected $table = 'dados_desportivos';

    protected $fillable = [
        'club_id',
        'atleta_id',
        'num_federacao',
        'cartao_federacao',
        'numero_pmb',
        'data_inscricao',
        'inscricao',
        'escalao_atual_id',
        'data_atestado_medico',
        'informacoes_medicas',
        'ativo',
    ];

    protected $casts = [
        'data_inscricao' => 'date',
        'data_atestado_medico' => 'date',
        'ativo' => 'boolean',
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
