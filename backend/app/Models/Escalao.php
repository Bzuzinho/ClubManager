<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Escalao extends Model
{
    protected $table = 'escaloes';

    public $timestamps = false;

    protected $guarded = [];

    public function atletas()
    {
        return $this->hasMany(AtletaEscalao::class);
    }
}
