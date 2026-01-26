<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Competicao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'competicoes';

    protected $fillable = [
        'modalidade_id',
        'equipa_casa_id',
        'adversario',
        'tipo',
        'data',
        'hora',
        'local',
        'casa',
        'competicao',
        'jornada',
        'estado',
        'golos_favor',
        'golos_contra',
        'resultado',
        'relatorio',
        'observacoes',
    ];

    protected $casts = [
        'data' => 'date',
        'casa' => 'boolean',
        'golos_favor' => 'integer',
        'golos_contra' => 'integer',
        'modalidade_id' => 'integer',
        'equipa_casa_id' => 'integer',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function modalidade()
    {
        return $this->belongsTo(Modalidade::class);
    }

    public function equipaCasa()
    {
        return $this->belongsTo(Equipa::class, 'equipa_casa_id');
    }

    public function convocatorias()
    {
        return $this->hasMany(Convocatoria::class);
    }

    /* =====================
     * SCOPES
     * ===================== */

    public function scopeAgendadas($query)
    {
        return $query->where('estado', 'agendado');
    }

    public function scopeFinalizadas($query)
    {
        return $query->where('estado', 'finalizado');
    }
}
