<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UbicacionGps extends Model
{
    use HasFactory;

    protected $table = 'ubicacion_gps';

    protected $fillable = [
        'servicio_id',
        'latitud',
        'longitud',
        'velocidad',
        'fecha_hora'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'latitud' => 'decimal:7',
        'longitud' => 'decimal:7',
        'velocidad' => 'decimal:2'
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }

    public function scopeUltimas($query, $limit = 10)
    {
        return $query->orderBy('fecha_hora', 'desc')->limit($limit);
    }

    public function getCoordenadasAttribute()
    {
        return [
            'lat' => $this->latitud,
            'lng' => $this->longitud
        ];
    }
}