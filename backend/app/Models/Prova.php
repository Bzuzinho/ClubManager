<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prova extends Model
{
    protected $fillable = [
        'club_id',
        'nome',
        'distancia_m',
        'modalidade',
        'individual',
        'ativo',
    ];

    protected $casts = [
        'individual' => 'boolean',
        'ativo' => 'boolean',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }
}
