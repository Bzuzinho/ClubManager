<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentroCusto extends Model
{
    use HasFactory;

    protected $table = 'centros_custo';

    protected $fillable = [
        'nome',
        'codigo',
        'descricao',
        'responsavel_id',
        'orcamento_anual',
        'ativo',
    ];

    protected $casts = [
        'orcamento_anual' => 'decimal:2',
        'ativo' => 'boolean',
        'responsavel_id' => 'integer',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function responsavel()
    {
        return $this->belongsTo(Membro::class, 'responsavel_id');
    }

    public function movimentos()
    {
        return $this->hasMany(MovimentoFinanceiro::class);
    }
}
