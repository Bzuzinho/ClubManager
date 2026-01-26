<?php

namespace App\Models;

use App\Models\Traits\HasClubScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelacaoUser extends Model
{
    use HasClubScope;

    protected $table = 'relacoes_users';

    protected $fillable = [
        'club_id',
        'user_origem_id',
        'user_destino_id',
        'tipo_relacao',
        'data_inicio',
        'data_fim',
        'ativo',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'ativo' => 'boolean',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function userOrigem(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_origem_id');
    }

    public function userDestino(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_destino_id');
    }
}
