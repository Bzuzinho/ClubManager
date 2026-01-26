<?php

namespace App\Models;

use App\Models\Traits\HasClubScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaturaItem extends Model
{
    use HasClubScope;

    protected $table = 'fatura_itens';

    protected $fillable = [
        'club_id',
        'fatura_id',
        'catalogo_item_id',
        'descricao',
        'valor_unitario',
        'quantidade',
        'imposto_percentual',
        'total_linha',
        'centro_custo_id',
    ];

    protected $casts = [
        'valor_unitario' => 'decimal:2',
        'imposto_percentual' => 'decimal:2',
        'total_linha' => 'decimal:2',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function fatura(): BelongsTo
    {
        return $this->belongsTo(Fatura::class);
    }

    public function catalogoItem(): BelongsTo
    {
        return $this->belongsTo(CatalogoFaturaItem::class, 'catalogo_item_id');
    }

    public function centroCusto(): BelongsTo
    {
        return $this->belongsTo(CentroCusto::class);
    }
}
