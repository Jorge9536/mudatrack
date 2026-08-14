<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chofer extends Model
{
    use HasFactory;

    protected $table = 'choferes';

    protected $fillable = [
        'nombre_completo',
        'telefono',
        'licencia',
        'disponible',
        'observaciones'
    ];

    protected $casts = [
        'disponible' => 'boolean'
    ];

    public function servicios()
    {
        return $this->hasMany(Servicio::class);
    }

    public function getNombreCompletoAttribute($value)
    {
        return ucwords(strtolower($value));
    }

    public function scopeDisponible($query)
    {
        return $query->where('disponible', true);
    }
}