<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model
{
    protected $table = 'pessoas';

    protected $guarded = [];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function membro()
    {
        return $this->hasOne(Membro::class);
    }

    public function utilizador()
    {
        return $this->hasOne(Utilizador::class);
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
}
