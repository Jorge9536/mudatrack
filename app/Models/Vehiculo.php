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


    public function isAvailable($fecha, $horaInicio, $horaFin)
    {
        return !Servicio::verificarDisponibilidad($fecha, $horaInicio, $horaFin, $this->id, 'vehiculo');
    }

    public function isAvailableForEdit($fecha, $horaInicio, $horaFin, $servicioId = null)
    {
        return !Servicio::verificarDisponibilidadConExcepcion($fecha, $horaInicio, $horaFin, $this->id, 'vehiculo', $servicioId);
    }

    public function scopeDisponible($query)
    {
        return $query->where('disponible', true);
    }

    public function getTipoLabelAttribute()
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function getChoferActualAttribute()
    {
        $servicioActivo = $this->servicios()
            ->whereIn('estado', ['confirmado', 'en_progreso'])
            ->with('chofer')
            ->first();
        
        return $servicioActivo ? $servicioActivo->chofer : null;
    }

    public function getServicioActivoAttribute()
    {
        return $this->servicios()
            ->whereIn('estado', ['confirmado', 'en_progreso'])
            ->with(['chofer', 'cliente'])
            ->first();
    }
}