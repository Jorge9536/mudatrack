<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google2fa_secret',
        'google2fa_enabled',
        'recovery_codes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
        'recovery_codes',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'google2fa_enabled' => 'boolean',
        'recovery_codes'    => 'array',
    ];

    public const ROL_ADMIN         = 'admin';
    public const ROL_RECEPCIONISTA = 'recepcionista';
    public const ROL_CHOFER        = 'chofer';
    public const ROL_AYUDANTE      = 'ayudante';

    public function chofer()
    {
        return $this->hasOne(Chofer::class);
    }

    public function ayudante()
    {
        return $this->hasOne(Ayudante::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROL_ADMIN;
    }

    public function isRecepcionista(): bool
    {
        return $this->role === self::ROL_RECEPCIONISTA;
    }

    public function isChofer(): bool
    {
        return $this->role === self::ROL_CHOFER;
    }

    public function isAyudante(): bool
    {
        return $this->role === self::ROL_AYUDANTE;
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    public static function rolesDisponibles(): array
    {
        return [
            self::ROL_ADMIN         => 'Administrador',
            self::ROL_RECEPCIONISTA => 'Recepcionista',
            self::ROL_CHOFER        => 'Chofer',
            self::ROL_AYUDANTE      => 'Ayudante',
        ];
    }
}