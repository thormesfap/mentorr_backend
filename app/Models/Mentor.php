<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Notifications\Notifiable;

class Mentor extends Model
{
    use HasFactory, Notifiable;

    public const PER_PAGE = 15;
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
        'habilidades',
        'mentorias'
    ];

    protected function preco():Attribute{
        return Attribute::make(
            get: fn ($value) => (float) $value / 100
        );
    }
    public function mentorias(): HasMany
    {
        return $this->hasMany(Mentoria::class);
    }

    public function sessoes(): HasManyThrough{
        return $this->hasManyThrough(SessaoMentoria::class, Mentoria::class);
    }

    public function sessoesFuturas(): HasManyThrough{
        return $this->hasManyThrough(SessaoMentoria::class, Mentoria::class)->where('sessao_mentorias.data_hora_inicio', '>', Carbon::now());
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
        return $this->belongsToMany(Habilidade::class, 'mentor_habilidade')->withTimestamps();
    }

    public function temConflitoHorario(Carbon $dataHoraInicio, ?int $duracaoMinutos = null): bool
    {
        $duracaoMinutos = $duracaoMinutos ?? $this->minutos_por_chamada;
        $dataHoraFim = $dataHoraInicio->copy()->addMinutes($duracaoMinutos);

        return $this->sessoesFuturas()
            ->where(function ($query) use ($dataHoraInicio, $dataHoraFim) {
                // Verifica se há sobreposição de horários
                $query->whereBetween('sessao_mentorias.data_hora_inicio', [$dataHoraInicio, $dataHoraFim])
                    ->orWhere(function ($q) use ($dataHoraInicio, $dataHoraFim) {
                        $q->where('sessao_mentorias.data_hora_inicio', '<=', $dataHoraInicio)
                            ->whereNotNull('sessao_mentorias.data_hora_termino')
                            ->where('sessao_mentorias.data_hora_termino', '>', $dataHoraInicio);
                    });
            })
            ->exists();
    }
}
