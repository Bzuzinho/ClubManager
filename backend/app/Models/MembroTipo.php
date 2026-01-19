<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembroTipo extends Model
{
    protected $table = 'membro_tipos';

    protected $guarded = [];

    protected $dates = [
        'data_inicio',
        'data_fim',
    ];

    public function membro()
    {
        return $this->belongsTo(Membro::class);
    }

    public function tipo()
    {
        return $this->belongsTo(TipoMembro::class, 'tipo_membro_id');
    }
}
