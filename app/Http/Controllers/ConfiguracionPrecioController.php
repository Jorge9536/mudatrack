<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionPrecio;
use App\Services\CotizacionService;
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

    public function update(Request $request, CotizacionService $cotizacionService)
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

        $cotizacionService->actualizarConfiguracion($validated);

        return redirect()->route('configuracion.precios')
            ->with('success', '✅ Precios actualizados exitosamente.');
    }
}