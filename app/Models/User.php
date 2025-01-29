<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telefone',
        'data_nascimento'
    ];

    protected $appends = ['role_names'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'roles'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function booted(): void
    {
        static::created(function ($user){
            $role = Role::where('name', 'role_user')->first();
            if($role){
                $user->roles()->attach($role);
                $user->save();
            }
        });
    }
    public function roles(): BelongsToMany{
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function mentor(): HasOne{
        return $this->hasOne(Mentor::class);
    }

    public function getRoleNamesAttribute(){
        return $this->roles->pluck('name')->toArray();
    }

    public function getJWTIdentifier()
    {
        return $this->email;
    }

    public function getAuthIdentifierName()
    {
        return 'email';
    }

    public function mentorias(): BelongsToMany{
        return $this->belongsToMany(Mentoria::class);
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'nome' => $this->nome,
            'email' => $this->email,
            'roles' => $this->getRoleNamesAttribute()
        ];
    }


}
