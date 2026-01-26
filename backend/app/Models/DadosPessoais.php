<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DadosPessoais extends Model
{
    protected $table = 'dados_pessoais';

    protected $fillable = [
        'user_id',
        'foto_perfil',
        'nome_completo',
        'data_nascimento',
        'nif',
        'cc',
        'morada',
        'codigo_postal',
        'localidade',
        'nacionalidade',
        'estado_civil',
        'ocupacao',
        'empresa',
        'escola',
        'sexo',
        'menor',
        'numero_irmaos',
        'contacto_telefonico',
        'email_secundario',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'menor' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
