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
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // 🔥 CONSTANTES DE ROLES
    public const ROL_ADMIN = 'admin';
    public const ROL_RECEPCIONISTA = 'recepcionista';
    public const ROL_CHOFER = 'chofer';

    // 🔥 MÉTODOS PARA VERIFICAR ROLES
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

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }
}