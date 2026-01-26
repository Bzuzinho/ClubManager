<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escalao extends Model
{
    use HasFactory;

    protected $table = 'escaloes';

    protected $fillable = [
        'nome',
        'codigo',
        'idade_minima',
        'idade_maxima',
        'ano_nascimento_inicio',
        'ano_nascimento_fim',
        'genero',
        'descricao',
        'ativo',
        'ordem',
    ];

    protected $casts = [
        'idade_minima' => 'integer',
        'idade_maxima' => 'integer',
        'ano_nascimento_inicio' => 'integer',
        'ano_nascimento_fim' => 'integer',
        'ativo' => 'boolean',
        'ordem' => 'integer',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function equipas()
    {
        return $this->hasMany(Equipa::class);
    }

    /* =====================
     * SCOPES
     * ===================== */

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true)->orderBy('ordem');
    }
}
