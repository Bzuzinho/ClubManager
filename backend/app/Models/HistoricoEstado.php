<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoEstado extends Model
{
    use HasFactory;

    protected $table = 'historico_estado';

    protected $fillable = [
        'entidade_type',
        'entidade_id',
        'estado_anterior',
        'estado_novo',
        'motivo',
        'observacoes',
        'alterado_por',
        'data_alteracao',
    ];

    protected $casts = [
        'entidade_id' => 'integer',
        'alterado_por' => 'integer',
        'data_alteracao' => 'datetime',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function entidade()
    {
        return $this->morphTo();
    }

    public function alteradoPor()
    {
        return $this->belongsTo(User::class, 'alterado_por');
    }
}
