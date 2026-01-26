<?php

namespace App\Models;

use App\Models\Traits\HasClubScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    use HasFactory, HasClubScope;

    protected $table = 'pagamentos';

    protected $fillable = [
        'fatura_id',
        'metodo_pagamento_id',
        'numero_pagamento',
        'data_pagamento',
        'valor',
        'referencia',
        'comprovativo',
        'estado',
        'observacoes',
        'registado_por',
        'confirmado_por',
        'data_confirmacao',
    ];

    protected $casts = [
        'data_pagamento' => 'date',
        'data_confirmacao' => 'datetime',
        'valor' => 'decimal:2',
        'fatura_id' => 'integer',
        'metodo_pagamento_id' => 'integer',
        'registado_por' => 'integer',
        'confirmado_por' => 'integer',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function fatura()
    {
        return $this->belongsTo(Fatura::class);
    }

    public function metodoPagamento()
    {
        return $this->belongsTo(MetodoPagamento::class);
    }

    public function registadoPor()
    {
        return $this->belongsTo(User::class, 'registado_por');
    }

    public function confirmadoPor()
    {
        return $this->belongsTo(User::class, 'confirmado_por');
    }

    public function movimentoFinanceiro()
    {
        return $this->hasOne(MovimentoFinanceiro::class);
    }
}
