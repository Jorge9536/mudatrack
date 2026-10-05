<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chofer extends Model
{
    use HasFactory;

    protected $table = 'choferes';

    protected $fillable = [
        'user_id',
        'nombre_completo',
        'telefono',
        'licencia',
        'disponible',
        'observaciones'
    ];

    protected $casts = [
        'disponible' => 'boolean'
    ];

    // ============================================
    // RELACIONES
    // ============================================
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function servicios()
    {
        return $this->hasMany(Servicio::class);
    }

    public function dispositivos()
    {
        return $this->hasMany(Dispositivo::class);
    }

    // ============================================
    // MÉTODOS DE DISPONIBILIDAD
    // ============================================
    public function isAvailable($fecha, $horaInicio, $horaFin)
    {
        return !Servicio::verificarDisponibilidad($fecha, $horaInicio, $horaFin, $this->id, 'chofer');
    }

    public function isAvailableForEdit($fecha, $horaInicio, $horaFin, $servicioId = null)
    {
        return !Servicio::verificarDisponibilidadConExcepcion($fecha, $horaInicio, $horaFin, $this->id, 'chofer', $servicioId);
    }

    // ============================================
    // ACCESSORS
    // ============================================
    public function getNombreCompletoAttribute($value)
    {
        return ucwords(strtolower($value));
    }

    // ============================================
    // SCOPES
    // ============================================
    public function scopeDisponible($query)
    {
        return $query->where('disponible', true);
    }
}