<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';

    protected $fillable = [
        'cliente_id',
        'vehiculo_id',
        'chofer_id',
        'origen',
        'destino',
        'fecha_servicio',
        'hora_inicio',
        'hora_fin',
        'cantidad_ayudantes',
        'numero_pisos',
        'es_callejon',
        'distancia_km',
        'metodo_pago', 
        'costo_total',
        'estado',
        'observaciones',
        'metodo_pago'
    ];

    protected $casts = [
        'fecha_servicio' => 'date',
        'hora_inicio' => 'datetime',
        'hora_fin' => 'datetime',
        'es_callejon' => 'boolean',
        'costo_total' => 'decimal:2'
    ];

    public const ESTADOS = [
        'pendiente',
        'confirmado',
        'en_progreso',
        'finalizado',
        'cancelado',
        'pendiente_pago',
        'pagado'
    ];

    public const ESTADOS_LABEL = [
        'pendiente' => 'Pendiente',
        'confirmado' => 'Confirmado',
        'en_progreso' => 'En Progreso',
        'finalizado' => 'Finalizado',
        'cancelado' => 'Cancelado',
        'pendiente_pago' => 'Pendiente de Pago',
        'pagado' => 'Pagado'
    ];

    public const METODOS_PAGO = [
        'efectivo' => 'Efectivo',
        'qr' => 'Código QR',
        'transferencia' => 'Transferencia'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function chofer()
    {
        return $this->belongsTo(Chofer::class);
    }

    public function bienes()
    {
        return $this->hasMany(Bien::class);
    }

    public function ubicacionesGps()
    {
        return $this->hasMany(UbicacionGps::class);
    }

    public function deuda()
    {
        return $this->hasOne(Deuda::class);
    }

    public function estaPagado(): bool
    {
        return $this->estado === 'pagado' || 
               ($this->deuda && $this->deuda->estado === 'pagado');
    }

    public function puedeTransicionar(string $nuevoEstado): bool
    {
        $transiciones = [
            'pendiente' => ['confirmado', 'cancelado'],
            'confirmado' => ['en_progreso', 'cancelado'],
            'en_progreso' => ['finalizado', 'cancelado'],
            'finalizado' => ['pagado', 'pendiente_pago'],
            'pendiente_pago' => ['pagado'],
            'pagado' => [],
            'cancelado' => []
        ];

        return in_array($nuevoEstado, $transiciones[$this->estado] ?? []);
    }

    public function getEstadoLabelAttribute()
    {
        return self::ESTADOS_LABEL[$this->estado] ?? $this->estado;
    }

    public function getMetodoPagoLabelAttribute()
    {
        return self::METODOS_PAGO[$this->metodo_pago] ?? $this->metodo_pago;
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeEnProgreso($query)
    {
        return $query->where('estado', 'en_progreso');
    }

    public function scopeFinalizados($query)
    {
        return $query->where('estado', 'finalizado');
    }

    public function scopePagados($query)
    {
        return $query->where('estado', 'pagado');
    }
}