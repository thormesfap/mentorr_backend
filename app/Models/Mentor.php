<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mentor extends Model
{
    protected $fillable = [
        'biografia',
        'curriculo',
        'preco',
        'tags',
        'minutos_por_chamada',
        'quantidade_chamadas',
        'user_id',
    ];

    protected $hidden = [
        'cargo_id',
        'empresa_id',
        'user_id'
    ];
    protected $with = [
        'cargo',
        'empresa',
        'user',
        'habilidades'
    ];

    protected function preco():Attribute{
        return Attribute::make(
            get: fn ($value) => (float) $value / 100
        );
    }
    public function mentoria(): HasMany
    {
        return $this->hasMany(Mentoria::class);
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function habilidades(): BelongsToMany
    {
        return $this->belongsToMany(Habilidade::class, 'mentor_habilidade');
    }
}
