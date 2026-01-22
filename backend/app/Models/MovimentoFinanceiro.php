<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimentoFinanceiro extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'movimentos_financeiros';

    protected $fillable = [
        'centro_custo_id',
        'categoria_movimento_id',
        'tipo',
        'numero_movimento',
        'data_movimento',
        'valor',
        'descricao',
        'observacoes',
        'pagamento_id',
        'documento_comprovativo',
        'registado_por',
    ];

    protected $casts = [
        'data_movimento' => 'date',
        'valor' => 'decimal:2',
        'centro_custo_id' => 'integer',
        'categoria_movimento_id' => 'integer',
        'pagamento_id' => 'integer',
        'registado_por' => 'integer',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function centroCusto()
    {
        return $this->belongsTo(CentroCusto::class);
    }

    public function categoriaMovimento()
    {
        return $this->belongsTo(CategoriaMovimento::class);
    }

    public function pagamento()
    {
        return $this->belongsTo(Pagamento::class);
    }

    public function registadoPor()
    {
        return $this->belongsTo(User::class, 'registado_por');
    }

    /* =====================
     * SCOPES
     * ===================== */

    public function scopeReceitas($query)
    {
        return $query->where('tipo', 'receita');
    }

    public function scopeDespesas($query)
    {
        return $query->where('tipo', 'despesa');
    }
}
