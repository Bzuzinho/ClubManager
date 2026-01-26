<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetodoPagamento extends Model
{
    use HasFactory;

    protected $table = 'metodos_pagamento';

    protected $fillable = [
        'nome',
        'codigo',
        'descricao',
        'requer_comprovativo',
        'ativo',
        'ordem',
    ];

    protected $casts = [
        'requer_comprovativo' => 'boolean',
        'ativo' => 'boolean',
        'ordem' => 'integer',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class);
    }

    /* =====================
     * SCOPES
     * ===================== */

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true)->orderBy('ordem');
    }
}
