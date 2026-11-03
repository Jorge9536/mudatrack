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
        $user = auth()->user();

        if ($user->isChofer() && $user->chofer) {
            if ($servicio->chofer_id !== $user->chofer->id) {
                abort(403, 'No puedes gestionar pagos de servicios que no te pertenecen.');
            }
        }

        $servicio->load(['cliente', 'bienes']);

        $historial = Servicio::where('cliente_id', $servicio->cliente_id)
            ->where('estado', 'pagado')
            ->orWhere('estado', 'pendiente_pago')
            ->orderBy('created_at', 'desc')
            ->get();

        $qr = ConfiguracionQr::first();
        if (!$qr) {
            $qr = new ConfiguracionQr();
        }

        return view('pagos.index', compact('servicio', 'historial', 'qr'));
    }

    public function registrarPago(Request $request, Servicio $servicio)
    {
        $user = auth()->user();

        // 🔥 Chofer: solo puede registrar pagos de SUS servicios
        if ($user->isChofer() && $user->chofer) {
            if ($servicio->chofer_id !== $user->chofer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes registrar pagos de servicios que no te pertenecen.',
                ], 403);
            }
        }

        $validated = $request->validate([
            'metodo_pago' => 'required|in:efectivo,qr,transferencia',
            'monto'       => 'required|numeric|min:0',
        ]);

        if ($validated['monto'] != $servicio->costo_total) {
            return response()->json([
                'success' => false,
                'message' => 'El monto no coincide con el total del servicio',
            ], 422);
        }

        $servicio->update([
            'estado_pago' => 'pagado',
            'metodo_pago' => $validated['metodo_pago'],
        ]);

        if ($servicio->deuda) {
            $servicio->deuda->update(['estado' => 'pagado']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pago registrado exitosamente',
            'estado'  => 'pagado',
        ]);
    }

    public function configuracionQr()
    {
        $qr = ConfiguracionQr::first();

        if (!$qr) {
            $qr = new ConfiguracionQr();
        }

        return view('pagos.configuracion-qr', compact('qr'));
    }

    public function actualizarQr(Request $request)
    {
        $validated = $request->validate([
            'imagen_qr' => 'nullable|image|max:2048',
            'url_qr'    => 'nullable|url',
        ]);

        $qr = ConfiguracionQr::first();
        if (!$qr) {
            $qr = new ConfiguracionQr();
        }

        if ($request->hasFile('imagen_qr')) {
            if ($qr->imagen_qr) {
                $oldPath = storage_path('app/public/' . $qr->imagen_qr);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file = $request->file('imagen_qr');
            $extension = $file->getClientOriginalExtension();
            $nombreArchivo = uniqid() . '.' . $extension;
            $path = $file->storeAs('qrs', $nombreArchivo, 'public');

            $qr->imagen_qr = $path;
            $qr->url_qr = null;
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