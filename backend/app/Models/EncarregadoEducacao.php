<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EncarregadoEducacao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'encarregados_educacao';

    protected $fillable = [
        'pessoa_id',
        'telemovel_alternativo',
        'email_alternativo',
        'profissao',
        'local_trabalho',
        'telefone_trabalho',
        'contacto_emergencia',
        'observacoes',
    ];

    protected $casts = [
        'contacto_emergencia' => 'boolean',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function atletas()
    {
        return $this->belongsToMany(Atleta::class, 'atletas_encarregados')
            ->withPivot('grau_parentesco', 'principal', 'autorizado_levantar', 'receber_notificacoes')
            ->withTimestamps();
    }
}
