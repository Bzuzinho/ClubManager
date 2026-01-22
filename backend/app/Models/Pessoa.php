<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pessoa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pessoas';

    protected $fillable = [
        'user_id',
        'nome_completo',
        'nif',
        'email',
        'telemovel',
        'telefone_fixo',
        'data_nascimento',
        'nacionalidade',
        'naturalidade',
        'numero_identificacao',
        'validade_identificacao',
        'morada',
        'codigo_postal',
        'localidade',
        'distrito',
        'foto_perfil',
        'observacoes',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'validade_identificacao' => 'date',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function membro()
    {
        return $this->hasOne(Membro::class);
    }

    public function encarregadoEducacao()
    {
        return $this->hasOne(EncarregadoEducacao::class);
    }

    public function relacoesOrigem()
    {
        return $this->hasMany(RelacaoPessoa::class, 'pessoa_origem_id');
    }

    public function relacoesDestino()
    {
        return $this->hasMany(RelacaoPessoa::class, 'pessoa_destino_id');
    }

    public function consentimentos()
    {
        return $this->hasMany(Consentimento::class);
    }

    public function documentos()
    {
        return $this->morphMany(Documento::class, 'documentavel');
    }
}
