<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemFatura extends Model
{
    use HasFactory;

    protected $table = 'itens_fatura';

    protected $fillable = [
        'fatura_id',
        'descricao',
        'quantidade',
        'preco_unitario',
        'subtotal',
        'desconto',
        'total',
        'observacoes',
    ];

    protected $casts = [
        'fatura_id' => 'integer',
        'quantidade' => 'integer',
        'preco_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'desconto' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function fatura()
    {
        return $this->belongsTo(Fatura::class);
    }
}
