<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Epoca extends Model
{
    protected $fillable = [
        'club_id',
        'nome',
        'ano_temporada',
        'data_inicio',
        'data_fim',
        'estado',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }
}
