<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoTipo extends Model
{
    protected $table = 'eventos_tipos';

    protected $fillable = [
        'club_id',
        'nome',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }
}
