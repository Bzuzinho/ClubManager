<?php

namespace App\Models;

use App\Models\Traits\HasClubScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventoParticipante extends Model
{
    use HasClubScope;

    protected $table = 'eventos_participantes';

    protected $fillable = [
        'club_id',
        'evento_id',
        'user_id',
        'membro_id',
        'estado_confirmacao',
        'justificacao',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membro(): BelongsTo
    {
        return $this->belongsTo(Membro::class);
    }
}
