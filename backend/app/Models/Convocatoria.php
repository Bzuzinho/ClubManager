<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Convocatoria extends Model
{
    use HasFactory;

    protected $table = 'convocatorias';

    protected $fillable = [
        'competicao_id',
        'atleta_id',
        'estado',
        'titular',
        'hora_concentracao',
        'local_concentracao',
        'observacoes',
        'convocado_por',
        'data_convocatoria',
    ];

    protected $casts = [
        'titular' => 'boolean',
        'data_convocatoria' => 'datetime',
        'competicao_id' => 'integer',
        'atleta_id' => 'integer',
        'convocado_por' => 'integer',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function competicao()
    {
        return $this->belongsTo(Competicao::class);
    }

    public function atleta()
    {
        return $this->belongsTo(Atleta::class);
    }

    public function convocadoPor()
    {
        return $this->belongsTo(User::class, 'convocado_por');
    }
}
