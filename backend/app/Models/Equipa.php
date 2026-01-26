<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'equipas';

    protected $fillable = [
        'modalidade_id',
        'escalao_id',
        'nome',
        'codigo',
        'genero',
        'temporada',
        'treinador_principal_id',
        'local_treino',
        'horario_treino',
        'estado',
        'observacoes',
    ];

    protected $casts = [
        'modalidade_id' => 'integer',
        'escalao_id' => 'integer',
        'treinador_principal_id' => 'integer',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function modalidade()
    {
        return $this->belongsTo(Modalidade::class);
    }

    public function escalao()
    {
        return $this->belongsTo(Escalao::class);
    }

    public function treinadorPrincipal()
    {
        return $this->belongsTo(Membro::class, 'treinador_principal_id');
    }

    public function atletas()
    {
        return $this->belongsToMany(Atleta::class, 'atletas_equipas')
            ->withPivot('data_inicio', 'data_fim', 'numero_camisola', 'posicao', 'titular', 'capitao', 'ativo', 'observacoes')
            ->withTimestamps();
    }

    public function treinos()
    {
        return $this->hasMany(Treino::class);
    }

    public function competicoesCasa()
    {
        return $this->hasMany(Competicao::class, 'equipa_casa_id');
    }

    public function dadosDesportivos()
    {
        return $this->hasMany(DadosDesportivosAtleta::class);
    }

    /* =====================
     * SCOPES
     * ===================== */

    public function scopeAtivas($query)
    {
        return $query->where('estado', 'ativa');
    }

    public function scopeTemporadaAtual($query, $temporada = null)
    {
        $temporada = $temporada ?? date('Y') . '/' . (date('Y') + 1);
        return $query->where('temporada', $temporada);
    }
}
