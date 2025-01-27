<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessaoMentoria extends Model
{
    protected $fillable = [
        'avaliacao',
        'data_hora_inicio',
        'data_hora_termino',
        'mentoria_id'
    ];
    public function mentoria(): BelongsTo
    {
        return $this->belongsTo(Mentoria::class);
    }
}
