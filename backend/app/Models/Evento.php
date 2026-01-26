<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HasClubScope;

class Evento extends Model
{
    use HasFactory, SoftDeletes, HasClubScope;

    protected $table = 'eventos';

    protected $fillable = [
        'tipo_evento_id',
        'titulo',
        'descricao',
        'data_inicio',
        'data_fim',
        'hora_inicio',
        'hora_fim',
        'local',
        'morada_completa',
        'preco',
        'vagas_totais',
        'vagas_disponiveis',
        'data_limite_inscricao',
        'publico',
        'requer_aprovacao',
        'estado',
        'imagem',
        'observacoes',
        'criado_por',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'data_limite_inscricao' => 'date',
        'preco' => 'decimal:2',
        'vagas_totais' => 'integer',
        'vagas_disponiveis' => 'integer',
        'publico' => 'boolean',
        'requer_aprovacao' => 'boolean',
        'tipo_evento_id' => 'integer',
        'criado_por' => 'integer',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function tipoEvento()
    {
        return $this->belongsTo(TipoEvento::class);
    }

    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function inscricoes()
    {
        return $this->hasMany(InscricaoEvento::class);
    }

    /* =====================
     * SCOPES
     * ===================== */

    public function scopePublicos($query)
    {
        return $query->where('publico', true);
    }

    public function scopePublicados($query)
    {
        return $query->where('estado', 'publicado');
    }
}
