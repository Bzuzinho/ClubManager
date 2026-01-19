<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DadosDesportivos extends Model
{
    protected $table = 'dados_desportivos';

    protected $guarded = [];

    protected $dates = [
        'data_inscricao',
        'data_atestado_medico',
    ];

    public function atleta()
    {
        return $this->belongsTo(Atleta::class);
    }
}
