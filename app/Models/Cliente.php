<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre_completo',
        'telefono',
        'direccion',
        'latitud',
        'longitud',
        'foto_casa',
        'observaciones',
        'bloqueado'
    ];

    protected $casts = [
        'bloqueado' => 'boolean',
        'latitud' => 'decimal:7',
        'longitud' => 'decimal:7'
    ];

    public function servicios()
    {
        return $this->hasMany(Servicio::class);
    }

    public function deudas()
    {
        return $this->hasMany(Deuda::class);
    }

    public function estaBloqueado(): bool
    {
        return $this->bloqueado || $this->deudas()->where('estado', 'pendiente')->exists();
    }

    public function getNombreCompletoAttribute($value)
    {
        return ucwords(strtolower($value));
    }
}