<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mensalidade extends Model
{
    protected $fillable = [
        'club_id',
        'nome',
        'regularidade_por_semana',
        'escalao_id',
        'valor',
        'ativo',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function escalao(): BelongsTo
    {
        return $this->belongsTo(Escalao::class);
    }
}
