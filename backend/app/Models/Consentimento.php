<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consentimento extends Model
{
    protected $table = 'consentimentos';

    protected $guarded = [];

    protected $casts = [
        'estado' => 'boolean',
    ];

    protected $dates = [
        'data',
    ];

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }
}
