<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modalidade extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'modalidades';

    protected $fillable = [
        'nome',
        'codigo',
        'descricao',
        'icone',
        'cor',
        'ativa',
        'ordem',
        'observacoes',
    ];

    protected $casts = [
        'ativa' => 'boolean',
        'ordem' => 'integer',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function equipas()
    {
        return $this->hasMany(Equipa::class);
    }

    public function competicoes()
    {
        return $this->hasMany(Competicao::class);
    }

    /* =====================
     * SCOPES
     * ===================== */

    public function scopeAtivas($query)
    {
        return $query->where('ativa', true)->orderBy('ordem');
    }
}
