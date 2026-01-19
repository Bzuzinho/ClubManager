<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membro extends Model
{
    protected $table = 'membros';

    protected $guarded = [];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function tipos()
    {
        return $this->hasMany(MembroTipo::class);
    }

    public function atleta()
    {
        return $this->hasOne(Atleta::class);
    }
}
