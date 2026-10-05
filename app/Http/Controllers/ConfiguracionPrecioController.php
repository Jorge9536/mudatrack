<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionPrecio;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class ConfiguracionPrecioController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $config = ConfiguracionPrecio::getConfig();
        return view('configuracion.precios', compact('config'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'precio_la_paz' => 'required|numeric|min:0',
            'precio_el_alto' => 'required|numeric|min:0',
            'precio_el_alto_la_paz' => 'required|numeric|min:0',
            'costo_ayudante' => 'required|numeric|min:0',
            'costo_piso_adicional' => 'required|numeric|min:0',
            'costo_callejon' => 'required|numeric|min:0',
            'costo_km_extra' => 'required|numeric|min:0'
        ]);

        // 🔥 OBTENER LA CONFIGURACIÓN Y ACTUALIZAR DIRECTAMENTE
        $config = ConfiguracionPrecio::first();
        if (!$config) {
            $config = new ConfiguracionPrecio();
        }

        $config->fill($validated);
        $config->save();

        return redirect()->route('configuracion.precios')
            ->with('success', '✅ Precios actualizados exitosamente.');
    }
}