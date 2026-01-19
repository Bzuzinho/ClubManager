<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Atleta extends Model
{
    protected $table = 'atletas';

    protected $guarded = [];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function membro()
    {
        return $this->belongsTo(Membro::class);
    }

    public function dadosDesportivos()
    {
        return $this->hasOne(DadosDesportivos::class);
    }

    public function escaloes()
    {
        return $this->hasMany(AtletaEscalao::class);
    }
}
