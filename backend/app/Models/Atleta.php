<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasClubScope;

class Atleta extends Model
{
    use HasFactory, HasClubScope;

    protected $table = 'atletas';

    protected $fillable = [
        'membro_id',
        'ativo',
        'numero_camisola',
        'tamanho_equipamento',
        'altura',
        'peso',
        'pe_dominante',
        'posicao_preferida',
        'observacoes_medicas',
        'observacoes',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'altura' => 'decimal:2',
        'peso' => 'decimal:2',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function membro()
    {
        return $this->belongsTo(Membro::class);
    }

    public function equipas()
    {
        return $this->belongsToMany(Equipa::class, 'atletas_equipas')
            ->withPivot('data_inicio', 'data_fim', 'numero_camisola', 'posicao', 'titular', 'capitao', 'ativo', 'observacoes')
            ->withTimestamps();
    }

    public function encarregados()
    {
        return $this->belongsToMany(EncarregadoEducacao::class, 'atletas_encarregados')
            ->withPivot('grau_parentesco', 'principal', 'autorizado_levantar', 'receber_notificacoes')
            ->withTimestamps();
    }

    public function presencasTreino()
    {
        return $this->hasMany(PresencaTreino::class);
    }

    public function convocatorias()
    {
        return $this->hasMany(Convocatoria::class);
    }

    public function dadosDesportivos()
    {
        return $this->hasMany(DadosDesportivosAtleta::class);
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
        return $query->where('ativo', true);
    }
}
