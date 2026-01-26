<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEvento extends Model
{
    use HasFactory;

    protected $table = 'tipos_evento';

    protected $fillable = [
        'nome',
        'codigo',
        'descricao',
        'cor',
        'icone',
        'requer_inscricao',
        'ativo',
        'ordem',
    ];

    protected $casts = [
        'requer_inscricao' => 'boolean',
        'ativo' => 'boolean',
        'ordem' => 'integer',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function eventos()
    {
        return $this->hasMany(Evento::class);
    }

    /* =====================
     * SCOPES
     * ===================== */

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true)->orderBy('ordem');
    }
}
