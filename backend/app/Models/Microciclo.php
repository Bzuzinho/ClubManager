<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Microciclo extends Model
{
    protected $fillable = [
        'club_id',
        'mesociclo_id',
        'nome',
        'data_inicio',
        'data_fim',
        'objetivo',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function mesociclo(): BelongsTo
    {
        return $this->belongsTo(Mesociclo::class);
    }
}
