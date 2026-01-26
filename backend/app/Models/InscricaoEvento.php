<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InscricaoEvento extends Model
{
    use HasFactory;

    protected $table = 'inscricoes_evento';

    protected $fillable = [
        'evento_id',
        'membro_id',
        'estado',
        'data_inscricao',
        'data_confirmacao',
        'pago',
        'valor_pago',
        'numero_acompanhantes',
        'observacoes',
    ];

    protected $casts = [
        'data_inscricao' => 'date',
        'data_confirmacao' => 'date',
        'pago' => 'boolean',
        'valor_pago' => 'decimal:2',
        'numero_acompanhantes' => 'integer',
        'evento_id' => 'integer',
        'membro_id' => 'integer',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function membro()
    {
        return $this->belongsTo(Membro::class);
    }

    /* =====================
     * SCOPES
     * ===================== */

    public function scopeConfirmadas($query)
    {
        return $query->where('estado', 'confirmada');
    }

    public function scopePendentes($query)
    {
        return $query->where('estado', 'pendente');
    }
}
