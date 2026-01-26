<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificacaoTipo extends Model
{
    protected $table = 'notificacoes_tipos';

    protected $fillable = [
        'slug',
        'nome',
        'descricao',
    ];
}
