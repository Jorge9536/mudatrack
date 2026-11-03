<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ayudante extends Model
{
    use HasFactory;

    protected $table = 'ayudantes';

    protected $fillable = [
        'user_id',
        'nombre_completo',
        'telefono',
        'disponible'
    ];

    protected $casts = [
        'disponible' => 'boolean'
    ];
    public function isAvailable($fecha, $horaInicio, $horaFin)
    {
        return !Servicio::verificarDisponibilidad($fecha, $horaInicio, $horaFin, $this->id, 'ayudante');
    }
    public function isAvailableForEdit($fecha, $horaInicio, $horaFin, $servicioId = null)
    {
        return !Servicio::verificarDisponibilidadConExcepcion($fecha, $horaInicio, $horaFin, $this->id, 'ayudante', $servicioId);
    }

    public function getNombreCompletoAttribute($value)
    {
        return ucwords(strtolower($value));
    }

    public function scopeDisponible($query)
    {
        return $query->where('disponible', true);
    }

    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'servicio_ayudante', 'ayudante_id', 'servicio_id')
                    ->withTimestamps();
    }
}