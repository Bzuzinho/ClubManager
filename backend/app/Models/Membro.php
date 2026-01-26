<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasClubScope;

class Membro extends Model
{
    use HasFactory, HasClubScope;

    protected $table = 'membros';

    protected $fillable = [
        'club_id',
        'user_id',
        'numero_socio',
        'estado',
        'data_adesao',
        'data_fim',
        'observacoes',
    ];

    protected $casts = [
        'data_adesao' => 'date',
        'data_fim' => 'date',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function atleta()
    {
        return $this->hasOne(Atleta::class, 'membro_id');
    }

    public function dadosFinanceiros()
    {
        return $this->hasOne(DadosFinanceiros::class, 'membro_id');
    }

    public function tiposUtilizador()
    {
        return $this->hasManyThrough(
            TipoUtilizador::class,
            UserTipoUtilizador::class,
            'user_id',
            'id',
            'user_id',
            'tipo_utilizador_id'
        );
    }

    public function faturas()
    {
        return $this->hasMany(Fatura::class, 'membro_id');
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

    public function scopeSuspensos($query)
    {
        return $query->where('estado', 'suspenso');
    }
}
