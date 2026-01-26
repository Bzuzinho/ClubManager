<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTipoUtilizador extends Model
{
    protected $table = 'user_tipos_utilizador';

    protected $fillable = [
        'club_id',
        'user_id',
        'tipo_utilizador_id',
        'data_inicio',
        'data_fim',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tipoUtilizador(): BelongsTo
    {
        return $this->belongsTo(TipoUtilizador::class);
    }
}
