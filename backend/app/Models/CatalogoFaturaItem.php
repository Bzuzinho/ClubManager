<?php

namespace App\Models;

use App\Models\Traits\HasClubScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogoFaturaItem extends Model
{
    use HasClubScope;

    protected $table = 'catalogo_fatura_itens';

    protected $fillable = [
        'club_id',
        'descricao',
        'valor_unitario',
        'imposto_percentual',
        'tipo',
        'ativo',
    ];

    protected $casts = [
        'valor_unitario' => 'decimal:2',
        'imposto_percentual' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }
}
