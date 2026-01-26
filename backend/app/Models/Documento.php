<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $table = 'documentos';

    protected $fillable = [
        'documentavel_type',
        'documentavel_id',
        'tipo_documento_id',
        'nome_original',
        'nome_ficheiro',
        'caminho',
        'mime_type',
        'tamanho',
        'data_emissao',
        'data_validade',
        'data_upload',
        'estado',
        'observacoes',
        'uploaded_by',
    ];

    protected $casts = [
        'data_emissao' => 'date',
        'data_validade' => 'date',
        'data_upload' => 'date',
        'tamanho' => 'integer',
    ];

    /* =====================
     * RELAÇÕES
     * ===================== */

    public function documentavel()
    {
        return $this->morphTo();
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /* =====================
     * SCOPES
     * ===================== */

    public function scopeValidos($query)
    {
        return $query->where('estado', 'valido');
    }

    public function scopeExpirados($query)
    {
        return $query->where('estado', 'expirado');
    }

    public function scopePendentes($query)
    {
        return $query->where('estado', 'pendente_validacao');
    }
}
