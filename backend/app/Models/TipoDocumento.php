<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    use HasFactory;

    protected $table = 'tipos_documento';

    protected $fillable = [
        'nome',
        'codigo',
        'descricao',
        'obrigatorio',
        'tem_validade',
        'validade_meses',
        'aplicavel_a',
        'ativo',
        'ordem',
    ];

    protected $casts = [
        'obrigatorio' => 'boolean',
        'tem_validade' => 'boolean',
        'ativo' => 'boolean',
        'validade_meses' => 'integer',
        'ordem' => 'integer',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }

    /* =====================
     * SCOPES
     * ===================== */

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true)->orderBy('ordem');
    }

    public function scopeObrigatorios($query)
    {
        return $query->where('obrigatorio', true);
    }
}
