<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Club extends Model
{
    protected $fillable = [
        'nome_fiscal',
        'abreviatura',
        'nif',
        'morada',
        'contacto_telefonico',
        'email',
        'logo_ficheiro_id',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function logoFicheiro(): BelongsTo
    {
        return $this->belongsTo(Ficheiro::class, 'logo_ficheiro_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(ClubUser::class);
    }

    public function membros(): HasMany
    {
        return $this->hasMany(Membro::class);
    }

    public function escaloes(): HasMany
    {
        return $this->hasMany(Escalao::class);
    }

    public function tiposUtilizador(): HasMany
    {
        return $this->hasMany(TipoUtilizador::class);
    }

    public function provas(): HasMany
    {
        return $this->hasMany(Prova::class);
    }

    public function centrosCusto(): HasMany
    {
        return $this->hasMany(CentroCusto::class);
    }

    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class);
    }

    public function faturas(): HasMany
    {
        return $this->hasMany(Fatura::class);
    }
}
