<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasClubScope;

class Treino extends Model
{
    use HasFactory, SoftDeletes, HasClubScope;

    protected $table = 'treinos';

    protected $fillable = [
        'equipa_id',
        'data',
        'hora_inicio',
        'hora_fim',
        'local',
        'tipo',
        'objetivos',
        'descricao',
        'observacoes',
        'responsavel_id',
        'estado',
        'motivo_cancelamento',
    ];

    protected $casts = [
        'data' => 'date',
        'equipa_id' => 'integer',
        'responsavel_id' => 'integer',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function equipa()
    {
        return $this->belongsTo(Equipa::class);
    }

    public function responsavel()
    {
        return $this->belongsTo(Membro::class, 'responsavel_id');
    }

    public function presencas()
    {
        return $this->hasMany(PresencaTreino::class);
    }

    /* =====================
     * SCOPES
     * ===================== */

    public function scopeAgendados($query)
    {
        return $query->where('estado', 'agendado');
    }

    public function scopeRealizados($query)
    {
        return $query->where('estado', 'realizado');
    }
}
