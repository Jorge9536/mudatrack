<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\Deuda;
use App\Models\ConfiguracionQr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class PagoController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Servicio $servicio)
    {
        $servicio->load(['cliente', 'bienes']);
        
        $historial = Servicio::where('cliente_id', $servicio->cliente_id)
            ->where('estado', 'pagado')
            ->orWhere('estado', 'pendiente_pago')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pagos.index', compact('servicio', 'historial'));
    }

    public function registrarPago(Request $request, Servicio $servicio)
    {
        $validated = $request->validate([
            'metodo_pago' => 'required|in:efectivo,qr,transferencia',
            'monto' => 'required|numeric|min:0'
        ]);

        if ($validated['monto'] != $servicio->costo_total) {
            return response()->json([
                'success' => false,
                'message' => 'El monto no coincide con el total del servicio'
            ], 422);
        }

        $servicio->update([
            'estado' => 'pagado',
            'metodo_pago' => $validated['metodo_pago']
        ]);

        if ($servicio->deuda) {
            $servicio->deuda->update(['estado' => 'pagado']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pago registrado exitosamente',
            'estado' => 'pagado'
        ]);
    }

    public function configuracionQr()
    {
        $qr = ConfiguracionQr::first() ?? new ConfiguracionQr();
        return view('pagos.configuracion-qr', compact('qr'));
    }

    public function actualizarQr(Request $request)
    {
        $validated = $request->validate([
            'imagen_qr' => 'nullable|image|max:2048',
            'url_qr' => 'nullable|url'
        ]);

        $qr = ConfiguracionQr::first() ?? new ConfiguracionQr();

        if ($request->hasFile('imagen_qr')) {
            $path = $request->file('imagen_qr')->store('qrs', 'public');
            $qr->imagen_qr = $path;
        }

        if ($request->filled('url_qr')) {
            $qr->url_qr = $request->url_qr;
        }

        $qr->fecha_actualizacion = now();
        $qr->save();

        return redirect()->route('pagos.configuracion-qr')
            ->with('success', 'QR actualizado exitosamente');
    }
}