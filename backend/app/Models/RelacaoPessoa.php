<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelacaoPessoa extends Model
{
    use HasFactory;

    protected $table = 'relacoes_pessoa';

    protected $fillable = [
        'pessoa_origem_id',
        'pessoa_destino_id',
        'tipo_relacao',
        'observacoes',
    ];

    protected $casts = [
        'pessoa_origem_id' => 'integer',
        'pessoa_destino_id' => 'integer',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function pessoaOrigem()
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_origem_id');
    }

    public function pessoaDestino()
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_destino_id');
    }
}
