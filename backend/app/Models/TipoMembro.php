<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoMembro extends Model
{
    protected $table = 'tipos_membro';

    public $timestamps = false;

    protected $guarded = [];

    public function membros()
    {
        return $this->hasMany(MembroTipo::class);
    }
}
