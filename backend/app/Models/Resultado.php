<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resultado extends Model
{
    protected $fillable = [
        'club_id',
        'evento_id',
        'atleta_id',
        'prova_id',
        'epoca_id',
        'piscina',
        'tempo',
        'classificacao',
        'pontos',
        'notas',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    public function atleta(): BelongsTo
    {
        return $this->belongsTo(Atleta::class);
    }

    public function prova(): BelongsTo
    {
        return $this->belongsTo(Prova::class);
    }

    public function epoca(): BelongsTo
    {
        return $this->belongsTo(Epoca::class);
    }
}
