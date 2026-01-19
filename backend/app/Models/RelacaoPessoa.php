<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelacaoPessoa extends Model
{
    protected $table = 'relacoes_pessoas';

    protected $guarded = [];

    protected $dates = [
        'data_inicio',
        'data_fim',
    ];

    public function origem()
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_origem_id');
    }

    public function destino()
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_destino_id');
    }
}
