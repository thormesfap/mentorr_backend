<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Habilidade extends Model
{
    protected $fillable = ['nome', 'area_id'];

    protected $with = ['area'];

    protected $hidden = ['area_id', 'pivot'];

    public const PER_PAGE = 15;

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
    public function mentores(): BelongsToMany
    {
        return $this->belongsToMany(Mentor::class, 'mentor_habilidade');
    }

}
