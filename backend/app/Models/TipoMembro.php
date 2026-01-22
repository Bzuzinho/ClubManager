<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoMembro extends Model
{
    use HasFactory;

    protected $table = 'tipos_membro';

    protected $fillable = [
        'nome',
        'codigo',
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

    public function membros()
    {
        return $this->belongsToMany(Membro::class, 'membros_tipos')
            ->withPivot('data_inicio', 'data_fim', 'ativo', 'observacoes')
            ->withTimestamps();
    }

    /* =====================
     * SCOPES
     * ===================== */

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true)->orderBy('ordem');
    }
}
