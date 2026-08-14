<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';

    protected $fillable = [
        'placa',
        'marca',
        'modelo',
        'tipo',
        'capacidad_kg',
        'disponible',
        'observaciones'
    ];

    protected $casts = [
        'disponible' => 'boolean',
        'capacidad_kg' => 'integer'
    ];

    public const TIPOS = [
        '3ton' => '3 Toneladas',
        '6ton' => '6 Toneladas',
        'chata' => 'Chata'
    ];

    public function servicios()
    {
        return $this->hasMany(Servicio::class);
    }

    public function scopeDisponible($query)
    {
        return $query->where('disponible', true);
    }

    public function getTipoLabelAttribute()
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }
}