<?php

namespace App\Services;

use App\Models\ConfiguracionPrecio;

class CotizacionService
{
    protected $config;

    public function __construct()
    {
        // Obtener configuración de precios desde la base de datos
        $this->config = ConfiguracionPrecio::getConfig();
    }

    /**
     * Calcular el costo total de una mudanza
     */
    public function calcular(array $params): float
    {
        $total = 0;

        // Costo base por zona
        $total += $this->obtenerCostoBase($params['zona'] ?? 'la_paz');
        
        // Costo por ayudantes
        $total += ($params['ayudantes'] ?? 0) * $this->config->costo_ayudante;
        
        // Costo por pisos adicionales
        $pisos = $params['pisos'] ?? 1;
        if ($pisos > 1) {
            $total += ($pisos - 1) * $this->config->costo_piso_adicional;
        }

        // Costo por callejón
        if ($params['es_callejon'] ?? false) {
            $total += $this->config->costo_callejon;
        }

        // Costo por kilómetros extras (más de 10km)
        if (isset($params['distancia_km']) && $params['distancia_km'] > 10) {
            $kmExtra = $params['distancia_km'] - 10;
            $total += $kmExtra * $this->config->costo_km_extra;
        }

        return $total;
    }

    /**
     * Obtener el costo base según la zona
     */
    public function obtenerCostoBase(string $zona): float
    {
        $tarifas = [
            'la_paz' => $this->config->precio_la_paz,
            'el_alto' => $this->config->precio_el_alto,
            'el_alto_a_la_paz' => $this->config->precio_el_alto_la_paz
        ];

        return $tarifas[$zona] ?? $this->config->precio_la_paz;
    }

    /**
     * Obtener desglose detallado de la cotización
     */
    public function obtenerDesglose(array $params): array
    {
        $zona = $params['zona'] ?? 'la_paz';
        $desglose = [
            'zona' => $this->obtenerCostoBase($zona),
            'ayudantes' => ($params['ayudantes'] ?? 0) * $this->config->costo_ayudante,
            'pisos_adicionales' => ($params['pisos'] ?? 1) > 1 ? 
                (($params['pisos'] - 1) * $this->config->costo_piso_adicional) : 0,
            'callejon' => ($params['es_callejon'] ?? false) ? $this->config->costo_callejon : 0,
            'km_extra' => 0
        ];

        if (isset($params['distancia_km']) && $params['distancia_km'] > 10) {
            $kmExtra = $params['distancia_km'] - 10;
            $desglose['km_extra'] = $kmExtra * $this->config->costo_km_extra;
        }

        $desglose['total'] = array_sum($desglose);
        return $desglose;
    }

    /**
     * Determinar la zona según origen y destino
     */
    public function determinarZona(string $origen, string $destino): string
    {
        $origenLimpio = strtolower(trim($origen));
        $destinoLimpio = strtolower(trim($destino));

        // Ambos en La Paz
        if (strpos($origenLimpio, 'la paz') !== false && 
            strpos($destinoLimpio, 'la paz') !== false) {
            return 'la_paz';
        }

        // Ambos en El Alto
        if (strpos($origenLimpio, 'el alto') !== false && 
            strpos($destinoLimpio, 'el alto') !== false) {
            return 'el_alto';
        }

        // Uno en La Paz y otro en El Alto (o viceversa)
        if ((strpos($origenLimpio, 'el alto') !== false && 
             strpos($destinoLimpio, 'la paz') !== false) ||
            (strpos($origenLimpio, 'la paz') !== false && 
             strpos($destinoLimpio, 'el alto') !== false)) {
            return 'el_alto_a_la_paz';
        }

        // Por defecto: La Paz
        return 'la_paz';
    }

    /**
     * Obtener la configuración actual de precios
     */
    public function getConfiguracion()
    {
        return $this->config;
    }

    /**
     * Actualizar la configuración de precios
     */
    public function actualizarConfiguracion(array $data)
    {
        $this->config->update($data);
        $this->config = ConfiguracionPrecio::getConfig();
    }
}