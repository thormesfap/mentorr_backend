<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $fillable = ['nome'];
    public function habilidades(): HasMany
    {
        return $this->hasMany(Habilidade::class);
    }
}
