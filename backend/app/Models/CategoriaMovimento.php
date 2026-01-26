<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaMovimento extends Model
{
    use HasFactory;

    protected $table = 'categorias_movimento';

    protected $fillable = [
        'nome',
        'codigo',
        'tipo',
        'descricao',
        'cor',
        'ativo',
        'ordem',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'ordem' => 'integer',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function movimentos()
    {
        return $this->hasMany(MovimentoFinanceiro::class);
    }

    /* =====================
     * SCOPES
     * ===================== */

    public function scopeAtivas($query)
    {
        return $query->where('ativo', true)->orderBy('ordem');
    }

    public function scopeReceitas($query)
    {
        return $query->where('tipo', 'receita');
    }

    public function scopeDespesas($query)
    {
        return $query->where('tipo', 'despesa');
    }
}
