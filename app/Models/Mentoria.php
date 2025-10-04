<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mentoria extends Model
{
    use HasFactory;
    protected $table = 'mentorias';
    protected $fillable = [
        'valor',
        'quantidade_sessoes',
        'expectativa',
        'data_hora_inicio',
        'data_hora_termino',
        'user_id',
        'mentor_id'
    ];

    protected $with = ['sessoes'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }

    public function sessoes(): HasMany
    {
        return $this->HasMany(SessaoMentoria::class)->orderBy('data_hora_inicio');
    }
}
