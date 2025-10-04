<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessaoMentoria extends Model
{
    use HasFactory;
    protected $fillable = [
        'avaliacao',
        'data_hora_inicio',
        'data_hora_termino',
        'mentoria_id'
    ];

    protected $attributes = [
        'avaliacao' => 0
    ];

    public function mentoria(): BelongsTo
    {
        return $this->belongsTo(Mentoria::class, 'mentoria_id');
    }
}
