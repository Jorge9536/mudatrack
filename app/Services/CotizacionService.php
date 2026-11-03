<?php

namespace App\Services;

use App\Models\ConfiguracionPrecio;

class CotizacionService
{
    protected $config;

    public function __construct()
    {
        $this->config = ConfiguracionPrecio::getConfig();
    }

    public function calcular(array $params): float
    {
        $total = 0;

        $total += $this->obtenerCostoBase($params['zona'] ?? 'la_paz');
        
        $total += ($params['ayudantes'] ?? 0) * $this->config->costo_ayudante;
        $pisos = $params['pisos'] ?? 1;
        if ($pisos > 1) {
            $total += ($pisos - 1) * $this->config->costo_piso_adicional;
        }

        if ($params['es_callejon'] ?? false) {
            $total += $this->config->costo_callejon;
        }

        if (isset($params['distancia_km']) && $params['distancia_km'] > 10) {
            $kmExtra = $params['distancia_km'] - 10;
            $total += $kmExtra * $this->config->costo_km_extra;
        }

        return $total;
    }

    public function obtenerCostoBase(string $zona): float
    {
        $tarifas = [
            'la_paz' => $this->config->precio_la_paz,
            'el_alto' => $this->config->precio_el_alto,
            'el_alto_a_la_paz' => $this->config->precio_el_alto_la_paz
        ];

        return $tarifas[$zona] ?? $this->config->precio_la_paz;
    }
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

    public function determinarZona(string $origen, string $destino): string
    {
        $origenLimpio = strtolower(trim($origen));
        $destinoLimpio = strtolower(trim($destino));

        if (strpos($origenLimpio, 'la paz') !== false && 
            strpos($destinoLimpio, 'la paz') !== false) {
            return 'la_paz';
        }

        if (strpos($origenLimpio, 'el alto') !== false && 
            strpos($destinoLimpio, 'el alto') !== false) {
            return 'el_alto';
        }

        if ((strpos($origenLimpio, 'el alto') !== false && 
             strpos($destinoLimpio, 'la paz') !== false) ||
            (strpos($origenLimpio, 'la paz') !== false && 
             strpos($destinoLimpio, 'el alto') !== false)) {
            return 'el_alto_a_la_paz';
        }

        return 'la_paz';
    }

    public function getConfiguracion()
    {
        return $this->config;
    }

    public function actualizarConfiguracion(array $data)
    {
        $this->config->update($data);
        $this->config = ConfiguracionPrecio::getConfig();
    }
}