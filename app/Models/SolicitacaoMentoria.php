<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SolicitacaoMentoria extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'mentor_id',
        'expectativa',
        'justificativa',
        'aceita',
        'data_hora_resposta'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }
}
