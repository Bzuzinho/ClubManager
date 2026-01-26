<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificacaoConfig extends Model
{
    protected $table = 'notificacoes_config';

    protected $fillable = [
        'club_id',
        'tipo_id',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(NotificacaoTipo::class, 'tipo_id');
    }
}
