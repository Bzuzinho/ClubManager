<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DadosConfiguracao extends Model
{
    protected $table = 'dados_configuracao';

    protected $fillable = [
        'club_id',
        'user_id',
        'rgpd',
        'rgpd_assinado',
        'data_rgpd',
        'arquivo_rgpd',
        'consentimento',
        'data_consentimento',
        'arquivo_consentimento',
        'afiliacao',
        'data_afiliacao',
        'arquivo_afiliacao',
        'declaracao_transporte',
        'declaracao_transporte_arquivo',
        'email_utilizador',
    ];

    protected $casts = [
        'rgpd' => 'boolean',
        'rgpd_assinado' => 'boolean',
        'consentimento' => 'boolean',
        'afiliacao' => 'boolean',
        'declaracao_transporte' => 'boolean',
        'data_rgpd' => 'date',
        'data_consentimento' => 'date',
        'data_afiliacao' => 'date',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
