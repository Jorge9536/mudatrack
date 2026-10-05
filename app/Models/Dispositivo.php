<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispositivo extends Model
{
    use HasFactory;

    protected $table = 'dispositivos';

    protected $fillable = [
        'dispositivo_id',
        'chofer_id',
        'vehiculo_id',
        'plataforma',
        'modelo',
        'activo',
        'ultima_conexion'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'ultima_conexion' => 'datetime'
    ];

    public function chofer()
    {
        return $this->belongsTo(Chofer::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }
}