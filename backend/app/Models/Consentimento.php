<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consentimento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'consentimentos';

    protected $fillable = [
        'pessoa_id',
        'tipo',
        'consentimento',
        'data_consentimento',
        'ip_address',
        'user_agent',
        'revogado',
        'data_revogacao',
        'observacoes',
    ];

    protected $casts = [
        'consentimento' => 'boolean',
        'revogado' => 'boolean',
        'data_consentimento' => 'datetime',
        'data_revogacao' => 'datetime',
        'pessoa_id' => 'integer',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }

    /* =====================
     * SCOPES
     * ===================== */

    public function scopeAtivos($query)
    {
        return $query->where('consentimento', true)->where('revogado', false);
    }
}
