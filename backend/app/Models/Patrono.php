<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patrono extends Model
{
    protected $fillable = [
        'club_id',
        'nome',
        'nif',
        'morada',
        'contacto',
        'email',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }
}
