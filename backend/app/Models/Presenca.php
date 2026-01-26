<?php

namespace App\Models;

use App\Models\Traits\HasClubScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presenca extends Model
{
    use HasClubScope;

    protected $fillable = [
        'club_id',
        'treino_id',
        'membro_id',
        'estado',
        'observacoes',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function treino(): BelongsTo
    {
        return $this->belongsTo(Treino::class);
    }

    public function membro(): BelongsTo
    {
        return $this->belongsTo(Membro::class);
    }
}
