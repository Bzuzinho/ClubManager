<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Membro extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'membros';

    protected $fillable = [
        'pessoa_id',
        'numero_socio',
        'estado',
        'data_inscricao',
        'data_inicio',
        'data_fim',
        'motivo_inativacao',
        'observacoes',
    ];

    protected $casts = [
        'data_inscricao' => 'date',
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function tipos()
    {
        return $this->belongsToMany(TipoMembro::class, 'membros_tipos')
            ->withPivot('data_inicio', 'data_fim', 'ativo', 'observacoes')
            ->withTimestamps();
    }

    public function atleta()
    {
        return $this->hasOne(Atleta::class);
    }

    public function faturas()
    {
        return $this->hasMany(Fatura::class);
    }

    public function inscricoesEvento()
    {
        return $this->hasMany(InscricaoEvento::class);
    }

    public function equipasTreinador()
    {
        return $this->hasMany(Equipa::class, 'treinador_principal_id');
    }

    public function centrosCusto()
    {
        return $this->hasMany(CentroCusto::class, 'responsavel_id');
    }

    public function documentos()
    {
        return $this->morphMany(Documento::class, 'documentavel');
    }

    /* =====================
     * SCOPES
     * ===================== */

    public function scopeAtivos($query)
    {
        return $query->where('estado', 'ativo');
    }

    public function scopeInativos($query)
    {
        return $query->where('estado', 'inativo');
    }

    public function scopePendentes($query)
    {
        return $query->where('estado', 'pendente');
    }
}
