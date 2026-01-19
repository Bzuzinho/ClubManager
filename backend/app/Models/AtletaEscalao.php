<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtletaEscalao extends Model
{
    protected $table = 'atleta_escalao';

    public $timestamps = false;

    protected $guarded = [];

    protected $dates = [
        'data_inicio',
        'data_fim',
    ];

    public function atleta()
    {
        return $this->belongsTo(Atleta::class);
    }

    public function escalao()
    {
        return $this->belongsTo(Escalao::class);
    }
}
