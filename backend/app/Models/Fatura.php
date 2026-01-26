<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasClubScope;

class Fatura extends Model
{
    use HasFactory, HasClubScope;

    protected $table = 'faturas';

    protected $fillable = [
        'membro_id',
        'numero_fatura',
        'data_emissao',
        'data_vencimento',
        'valor_total',
        'valor_pago',
        'valor_pendente',
        'estado',
        'tipo',
        'referencia_mb',
        'observacoes',
        'emitida_por',
    ];

    protected $casts = [
        'data_emissao' => 'date',
        'data_vencimento' => 'date',
        'valor_total' => 'decimal:2',
        'valor_pago' => 'decimal:2',
        'valor_pendente' => 'decimal:2',
        'membro_id' => 'integer',
        'emitida_por' => 'integer',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function membro()
    {
        return $this->belongsTo(Membro::class);
    }

    public function itens()
    {
        return $this->hasMany(ItemFatura::class);
    }

    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class);
    }

    public function emitidaPor()
    {
        return $this->belongsTo(User::class, 'emitida_por');
    }

    /* =====================
     * SCOPES
     * ===================== */

    public function scopePendentes($query)
    {
        return $query->where('estado', 'pendente');
    }

    public function scopePagas($query)
    {
        return $query->where('estado', 'paga');
    }

    public function scopeVencidas($query)
    {
        return $query->where('estado', 'vencida');
    }
}
