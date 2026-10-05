<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';

    protected $fillable = [
        'token_seguimiento',
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
        'estado_pago',
        'observaciones'
    ];

    protected $casts = [
        'fecha_servicio' => 'date',
        'hora_inicio' => 'string',
        'hora_fin' => 'string',
        'es_callejon' => 'boolean',
        'costo_total' => 'decimal:2'
    ];

    // ============================================
    // ESTADOS OPERATIVOS (ya sin 'pagado' ni 'pendiente_pago')
    // ============================================
    public const ESTADOS = [
        'pendiente',
        'confirmado',
        'en_progreso',
        'finalizado',
        'cancelado'
    ];

    public const ESTADOS_LABEL = [
        'pendiente' => 'Pendiente',
        'confirmado' => 'Confirmado',
        'en_progreso' => 'En Progreso',
        'finalizado' => 'Finalizado',
        'cancelado' => 'Cancelado'
    ];

    // ============================================
    // ESTADOS DE PAGO
    // ============================================
    public const ESTADOS_PAGO = [
        'pendiente',
        'pagado'
    ];

    public const ESTADOS_PAGO_LABEL = [
        'pendiente' => 'Pendiente',
        'pagado' => 'Pagado'
    ];

    public const METODOS_PAGO = [
        'efectivo' => 'Efectivo',
        'qr' => 'Código QR',
        'transferencia' => 'Transferencia'
    ];

    // ============================================
    // GENERAR TOKEN AUTOMÁTICAMENTE AL CREAR
    // ============================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($servicio) {
            if (empty($servicio->token_seguimiento)) {
                $servicio->token_seguimiento = Str::random(48);
            }
            // Inicializar estado_pago si no viene
            if (empty($servicio->estado_pago)) {
                $servicio->estado_pago = 'pendiente';
            }
        });
    }

    // ============================================
    // RELACIONES
    // ============================================
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

    public function ayudantes()
    {
        return $this->belongsToMany(Ayudante::class, 'servicio_ayudante', 'servicio_id', 'ayudante_id')
                    ->withTimestamps();
    }

    // ============================================
    // MÉTODOS PARA EL CALENDARIO
    // ============================================
    public function getFechaHoraInicioAttribute()
    {
        if ($this->fecha_servicio && $this->hora_inicio) {
            return \Carbon\Carbon::parse($this->fecha_servicio->format('Y-m-d') . ' ' . 
                                       \Carbon\Carbon::parse($this->hora_inicio)->format('H:i:s'));
        }
        return null;
    }

    public function getFechaHoraFinAttribute()
    {
        if ($this->fecha_servicio && $this->hora_fin) {
            return \Carbon\Carbon::parse($this->fecha_servicio->format('Y-m-d') . ' ' . 
                                       \Carbon\Carbon::parse($this->hora_fin)->format('H:i:s'));
        }
        return null;
    }

    public static function verificarDisponibilidad($fecha, $horaInicio, $horaFin, $recursoId = null, $tipo = 'vehiculo')
    {
        $inicio = \Carbon\Carbon::parse($fecha . ' ' . $horaInicio);
        $fin = \Carbon\Carbon::parse($fecha . ' ' . $horaFin);

        $query = self::whereDate('fecha_servicio', $fecha)
            ->whereIn('estado', ['pendiente', 'confirmado', 'en_progreso'])
            ->where(function($q) use ($inicio, $fin) {
                $q->where(function($sub) use ($inicio, $fin) {
                    $sub->whereTime('hora_inicio', '>=', $inicio->format('H:i:s'))
                        ->whereTime('hora_inicio', '<', $fin->format('H:i:s'));
                })->orWhere(function($sub) use ($inicio, $fin) {
                    $sub->whereTime('hora_inicio', '<=', $inicio->format('H:i:s'))
                        ->whereTime('hora_fin', '>', $inicio->format('H:i:s'));
                });
            });

        if ($recursoId && $tipo) {
            $campo = $tipo === 'vehiculo' ? 'vehiculo_id' : 
                     ($tipo === 'chofer' ? 'chofer_id' : null);
            if ($campo) {
                $query->where($campo, $recursoId);
            }
        }

        if ($tipo === 'ayudante' && $recursoId) {
            return self::whereHas('ayudantes', function($q) use ($recursoId, $fecha, $inicio, $fin) {
                $q->where('ayudante_id', $recursoId)
                  ->whereDate('fecha_servicio', $fecha)
                  ->whereIn('estado', ['pendiente', 'confirmado', 'en_progreso'])
                  ->where(function($sub) use ($inicio, $fin) {
                      $sub->whereTime('hora_inicio', '>=', $inicio->format('H:i:s'))
                          ->whereTime('hora_inicio', '<', $fin->format('H:i:s'))
                          ->orWhere(function($s) use ($inicio, $fin) {
                              $s->whereTime('hora_inicio', '<=', $inicio->format('H:i:s'))
                                ->whereTime('hora_fin', '>', $inicio->format('H:i:s'));
                          });
                  });
            })->exists();
        }

        return $query->exists();
    }

    public static function verificarDisponibilidadConExcepcion($fecha, $horaInicio, $horaFin, $recursoId = null, $tipo = 'vehiculo', $servicioId = null)
    {
        $inicio = \Carbon\Carbon::parse($fecha . ' ' . $horaInicio);
        $fin = \Carbon\Carbon::parse($fecha . ' ' . $horaFin);

        $query = self::whereDate('fecha_servicio', $fecha)
            ->whereIn('estado', ['pendiente', 'confirmado', 'en_progreso'])
            ->where(function($q) use ($inicio, $fin) {
                $q->where(function($sub) use ($inicio, $fin) {
                    $sub->whereTime('hora_inicio', '>=', $inicio->format('H:i:s'))
                        ->whereTime('hora_inicio', '<', $fin->format('H:i:s'));
                })->orWhere(function($sub) use ($inicio, $fin) {
                    $sub->whereTime('hora_inicio', '<=', $inicio->format('H:i:s'))
                        ->whereTime('hora_fin', '>', $inicio->format('H:i:s'));
                });
            });

        if ($servicioId) {
            $query->where('id', '!=', $servicioId);
        }

        if ($recursoId && $tipo) {
            $campo = $tipo === 'vehiculo' ? 'vehiculo_id' : 
                     ($tipo === 'chofer' ? 'chofer_id' : null);
            if ($campo) {
                $query->where($campo, $recursoId);
            }
        }

        if ($tipo === 'ayudante' && $recursoId) {
            return self::whereHas('ayudantes', function($q) use ($recursoId, $fecha, $inicio, $fin, $servicioId) {
                $q->where('ayudante_id', $recursoId)
                  ->whereDate('fecha_servicio', $fecha)
                  ->whereIn('estado', ['pendiente', 'confirmado', 'en_progreso'])
                  ->where(function($sub) use ($inicio, $fin) {
                      $sub->whereTime('hora_inicio', '>=', $inicio->format('H:i:s'))
                          ->whereTime('hora_inicio', '<', $fin->format('H:i:s'))
                          ->orWhere(function($s) use ($inicio, $fin) {
                              $s->whereTime('hora_inicio', '<=', $inicio->format('H:i:s'))
                                ->whereTime('hora_fin', '>', $inicio->format('H:i:s'));
                          });
                  });
                if ($servicioId) {
                    $q->where('servicio_id', '!=', $servicioId);
                }
            })->exists();
        }

        return $query->exists();
    }

    // ============================================
    // MÉTODOS DE LÓGICA
    // ============================================
    public function estaPagado(): bool
    {
        return $this->estado_pago === 'pagado';
    }

    public function puedeTransicionar(string $nuevoEstado): bool
    {
        $transiciones = [
            'pendiente' => ['confirmado', 'cancelado'],
            'confirmado' => ['en_progreso', 'cancelado'],
            'en_progreso' => ['finalizado', 'cancelado'],
            'finalizado' => [],
            'cancelado' => []
        ];

        return in_array($nuevoEstado, $transiciones[$this->estado] ?? []);
    }

    public function getEstadoLabelAttribute()
    {
        return self::ESTADOS_LABEL[$this->estado] ?? $this->estado;
    }

    public function getEstadoPagoLabelAttribute()
    {
        return self::ESTADOS_PAGO_LABEL[$this->estado_pago] ?? $this->estado_pago;
    }

    public function getMetodoPagoLabelAttribute()
    {
        return self::METODOS_PAGO[$this->metodo_pago] ?? $this->metodo_pago;
    }

    // ============================================
    // SCOPES
    // ============================================
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
        return $query->where('estado_pago', 'pagado');
    }

    public function scopePendientePago($query)
    {
        return $query->where('estado_pago', 'pendiente');
    }
}