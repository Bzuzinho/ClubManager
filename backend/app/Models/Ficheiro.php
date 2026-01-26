<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ficheiro extends Model
{
    protected $fillable = [
        'disk',
        'path',
        'original_name',
        'mime',
        'size_bytes',
        'checksum',
        'uploaded_by',
    ];

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
