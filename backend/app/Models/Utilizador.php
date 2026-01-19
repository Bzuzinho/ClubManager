<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Utilizador extends Authenticatable
{
    protected $table = 'utilizadores';

    protected $guarded = [];

    protected $hidden = [
        'password',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }
}
