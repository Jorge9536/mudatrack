<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionPrecio extends Model
{
    use HasFactory;

    protected $table = 'configuracion_precios';

    protected $fillable = [
        'precio_la_paz',
        'precio_el_alto',
        'precio_el_alto_la_paz',
        'costo_ayudante',
        'costo_piso_adicional',
        'costo_callejon',
        'costo_km_extra'
    ];

    protected $casts = [
        'precio_la_paz' => 'decimal:2',
        'precio_el_alto' => 'decimal:2',
        'precio_el_alto_la_paz' => 'decimal:2',
        'costo_ayudante' => 'decimal:2',
        'costo_piso_adicional' => 'decimal:2',
        'costo_callejon' => 'decimal:2',
        'costo_km_extra' => 'decimal:2'
    ];

    public static function getConfig()
    {
        return self::first() ?? self::create([
            'precio_la_paz' => 300.00,
            'precio_el_alto' => 200.00,
            'precio_el_alto_la_paz' => 250.00,
            'costo_ayudante' => 80.00,
            'costo_piso_adicional' => 20.00,
            'costo_callejon' => 30.00,
            'costo_km_extra' => 5.00
        ]);
    }
}