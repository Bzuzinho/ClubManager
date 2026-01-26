<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\HasClubScope;

class Grupo extends Model
{
    use HasClubScope;
    protected $fillable = [
        'club_id',
        'nome',
        'escalao_id',
        'treinador_user_id',
        'horario',
        'local',
        'ativo',
    ];

    protected $casts = [
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

    public function treinador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'treinador_user_id');
    }

    public function membros(): HasMany
    {
        return $this->hasMany(GrupoMembro::class);
    }

    public function treinos(): HasMany
    {
        return $this->hasMany(Treino::class);
    }
}
