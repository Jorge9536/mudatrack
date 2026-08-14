<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bien extends Model
{
    use HasFactory;

    protected $table = 'bienes';

    protected $fillable = [
        'servicio_id',
        'nombre',
        'cantidad',
        'descripcion'
    ];

    protected $casts = [
        'cantidad' => 'integer'
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }

    public function getNombreAttribute($value)
    {
        return ucwords(strtolower($value));
    }
}