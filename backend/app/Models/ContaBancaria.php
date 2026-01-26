<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContaBancaria extends Model
{
    use HasFactory;

    protected $table = 'contas_bancarias';

    protected $fillable = [
        'nome',
        'banco',
        'iban',
        'swift',
        'saldo_atual',
        'moeda',
        'conta_principal',
        'ativa',
        'observacoes',
    ];

    protected $casts = [
        'saldo_atual' => 'decimal:2',
        'conta_principal' => 'boolean',
        'ativa' => 'boolean',
    ];

    /* =====================
     * SCOPES
     * ===================== */

    public function scopeAtivas($query)
    {
        return $query->where('ativa', true);
    }

    public function scopePrincipal($query)
    {
        return $query->where('conta_principal', true);
    }
}
