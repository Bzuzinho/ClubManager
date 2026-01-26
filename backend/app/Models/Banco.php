<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Banco extends Model
{
    protected $fillable = [
        'club_id',
        'nome',
        'iban',
        'swift_bic',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }
}
