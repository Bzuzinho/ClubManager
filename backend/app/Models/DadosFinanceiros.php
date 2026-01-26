<?php

namespace App\Models;

use App\Models\Traits\HasClubScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DadosFinanceiros extends Model
{
    use HasClubScope;

    protected $table = 'dados_financeiros';

    protected $fillable = [
        'club_id',
        'membro_id',
        'mensalidade_id',
        'conta_corrente',
        'dia_cobranca',
        'observacoes',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function membro(): BelongsTo
    {
        return $this->belongsTo(Membro::class);
    }

    public function mensalidade(): BelongsTo
    {
        return $this->belongsTo(Mensalidade::class);
    }
    
    public function centrosCusto(): BelongsToMany
    {
        return $this->belongsToMany(
            CentroCusto::class,
            'membro_centros_custo',
            'membro_id',
            'centro_custo_id'
        )->where('membro_centros_custo.club_id', session('club_id'));
    }
}
