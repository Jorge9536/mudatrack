<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deuda extends Model
{
    use HasFactory;

    protected $table = 'deudas';

    protected $fillable = [
        'cliente_id',
        'servicio_id',
        'monto',
        'fecha_vencimiento',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'monto' => 'decimal:2'
    ];

    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'pagado' => 'Pagado',
        'vencido' => 'Vencido'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeVencidas($query)
    {
        return $query->where('estado', 'vencido')
            ->orWhere(function($q) {
                $q->where('estado', 'pendiente')
                  ->where('fecha_vencimiento', '<', now());
            });
    }

    public function estaVencida(): bool
    {
        return $this->estado === 'vencido' || 
               ($this->estado === 'pendiente' && $this->fecha_vencimiento < now());
    }

    public function getEstadoLabelAttribute()
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }
}