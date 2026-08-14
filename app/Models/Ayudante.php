<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ayudante extends Model
{
    use HasFactory;

    protected $table = 'ayudantes';

    protected $fillable = [
        'nombre_completo',
        'telefono',
        'disponible'
    ];

    protected $casts = [
        'disponible' => 'boolean'
    ];

    public function getNombreCompletoAttribute($value)
    {
        return ucwords(strtolower($value));
    }

    public function scopeDisponible($query)
    {
        return $query->where('disponible', true);
    }
}