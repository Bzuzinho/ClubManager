<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresencaTreino extends Model
{
    use HasFactory;

    protected $table = 'presencas_treino';

    protected $fillable = [
        'treino_id',
        'atleta_id',
        'estado',
        'hora_chegada',
        'hora_saida',
        'justificacao',
        'observacoes',
        'registado_por',
    ];

    protected $casts = [
        'treino_id' => 'integer',
        'atleta_id' => 'integer',
        'registado_por' => 'integer',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function treino()
    {
        return $this->belongsTo(Treino::class);
    }

    public function atleta()
    {
        return $this->belongsTo(Atleta::class);
    }

    public function registadoPor()
    {
        return $this->belongsTo(User::class, 'registado_por');
    }

    /* =====================
     * SCOPES
     * ===================== */

    public function scopePresentes($query)
    {
        return $query->where('estado', 'presente');
    }

    public function scopeAusentes($query)
    {
        return $query->where('estado', 'ausente');
    }
}
