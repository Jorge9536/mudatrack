<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Bien;
use App\Models\Deuda;
use App\Models\Chofer;
use App\Models\Vehiculo;
use App\Models\Ayudante;
use App\Models\ConfiguracionPrecio;
use App\Services\CotizacionService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controller as BaseController;

class ServicioController extends BaseController
{
    protected $cotizacionService;

    public function __construct(CotizacionService $cotizacionService)
    {
        $this->cotizacionService = $cotizacionService;
        $this->middleware('auth');
    }

    /**
     * Lista todos los servicios
     */
    public function index()
    {
        $servicios = Servicio::with(['cliente', 'chofer', 'vehiculo'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('servicios.index', compact('servicios'));
    }

    /**
     * Muestra el formulario para crear un nuevo servicio
     */
    public function create()
    {
        $clientes = Cliente::all();
        $choferes = Chofer::where('disponible', true)->get();
        $vehiculos = Vehiculo::where('disponible', true)->get();
        $ayudantes = Ayudante::where('disponible', true)->get();

        // 🔥 OBTENER CONFIGURACIÓN DE PRECIOS DESDE LA BASE DE DATOS
        $configPrecios = ConfiguracionPrecio::getConfig();

        return view('servicios.create', compact('clientes', 'choferes', 'vehiculos', 'ayudantes', 'configPrecios'));
    }

    /**
     * Guarda un nuevo servicio
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'origen' => 'required|string|max:255',
            'destino' => 'required|string|max:255',
            'fecha_servicio' => 'required|date|after:today',
            'cantidad_ayudantes' => 'required|integer|min:0',
            'numero_pisos' => 'required|integer|min:1',
            'es_callejon' => 'boolean',
            'distancia_km' => 'nullable|numeric|min:0',
            'bienes' => 'array',
            'bienes.*.nombre' => 'required|string',
            'bienes.*.cantidad' => 'required|integer|min:1',
            'bienes.*.descripcion' => 'nullable|string',
            'observaciones' => 'nullable|string'
        ]);

        $cliente = Cliente::find($validated['cliente_id']);
        if ($cliente->estaBloqueado()) {
            return redirect()->back()
                ->with('error', 'El cliente tiene deudas pendientes. No puede solicitar nuevos servicios.')
                ->withInput();
        }

        $zona = $this->cotizacionService->determinarZona(
            $validated['origen'],
            $validated['destino']
        );

        $costo = $this->cotizacionService->calcular([
            'zona' => $zona,
            'ayudantes' => $validated['cantidad_ayudantes'],
            'pisos' => $validated['numero_pisos'],
            'es_callejon' => $validated['es_callejon'] ?? false,
            'distancia_km' => $validated['distancia_km'] ?? 0
        ]);

        $servicio = Servicio::create([
            'cliente_id' => $validated['cliente_id'],
            'origen' => $validated['origen'],
            'destino' => $validated['destino'],
            'fecha_servicio' => $validated['fecha_servicio'],
            'cantidad_ayudantes' => $validated['cantidad_ayudantes'],
            'numero_pisos' => $validated['numero_pisos'],
            'es_callejon' => $validated['es_callejon'] ?? false,
            'distancia_km' => $validated['distancia_km'] ?? 0,
            'costo_total' => $costo,
            'estado' => 'pendiente',
            'observaciones' => $validated['observaciones'] ?? null
        ]);

        if (isset($validated['bienes'])) {
            foreach ($validated['bienes'] as $bien) {
                Bien::create([
                    'servicio_id' => $servicio->id,
                    'nombre' => $bien['nombre'],
                    'cantidad' => $bien['cantidad'],
                    'descripcion' => $bien['descripcion'] ?? null
                ]);
            }
        }

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio creado exitosamente. Costo total: ' . 
                number_format($costo, 2) . ' Bs');
    }

    /**
     * Muestra los detalles de un servicio
     */
    public function show(Servicio $servicio)
    {
        $servicio->load(['cliente', 'bienes', 'chofer', 'vehiculo', 'deuda']);
        return view('servicios.show', compact('servicio'));
    }

    /**
     * Actualiza el estado de un servicio
     */
    public function updateStatus(Request $request, Servicio $servicio)
    {
        $validated = $request->validate([
            'estado' => 'required|in:' . implode(',', Servicio::ESTADOS)
        ]);

        if (!$servicio->puedeTransicionar($validated['estado'])) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede cambiar de ' . $servicio->estado . ' a ' . $validated['estado']
            ], 422);
        }

        if ($validated['estado'] === 'finalizado' && !$servicio->estaPagado()) {
            $servicio->update(['estado' => 'pendiente_pago']);
            
            Deuda::create([
                'cliente_id' => $servicio->cliente_id,
                'servicio_id' => $servicio->id,
                'monto' => $servicio->costo_total,
                'fecha_vencimiento' => now()->addDays(1),
                'estado' => 'pendiente',
                'observaciones' => 'Servicio finalizado sin pago'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'El servicio no ha sido pagado. Se ha registrado una deuda.',
                'estado' => 'pendiente_pago'
            ]);
        }

        $servicio->update(['estado' => $validated['estado']]);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'estado' => $servicio->estado
        ]);
    }

    /**
     * Muestra el formulario de asignación de personal
     */
    public function showAsignarForm(Servicio $servicio)
    {
        $choferes = Chofer::where('disponible', true)->get();
        $vehiculos = Vehiculo::where('disponible', true)->get();
        $ayudantes = Ayudante::where('disponible', true)->get();

        return view('servicios.asignar', compact('servicio', 'choferes', 'vehiculos', 'ayudantes'));
    }

    /**
     * Asigna personal a un servicio
     */
    public function assignPersonal(Request $request, Servicio $servicio)
    {
        $validated = $request->validate([
            'chofer_id' => 'required|exists:choferes,id',
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'ayudantes' => 'array',
            'ayudantes.*' => 'exists:ayudantes,id'
        ]);

        $servicio->update([
            'chofer_id' => $validated['chofer_id'],
            'vehiculo_id' => $validated['vehiculo_id'],
            'estado' => 'confirmado'
        ]);

        return redirect()->route('servicios.show', $servicio)
            ->with('success', 'Personal asignado correctamente');
    }

    /**
     * Genera el comprobante PDF de un servicio
     */
    public function generarComprobante(Servicio $servicio)
    {
        $servicio->load(['cliente', 'bienes', 'chofer', 'vehiculo']);
        
        $pdf = Pdf::loadView('pdf.comprobante', compact('servicio'));
        
        return $pdf->download('comprobante-' . $servicio->id . '.pdf');
    }

    /**
     * Registra el pago de un servicio
     */
    public function registrarPago(Request $request, Servicio $servicio)
    {
        $validated = $request->validate([
            'metodo_pago' => 'required|in:efectivo,qr,transferencia',
            'monto' => 'nullable|numeric|min:0'
        ]);

        if ($servicio->estado === 'cancelado') {
            return response()->json([
                'success' => false,
                'message' => '❌ El servicio está cancelado, no se puede registrar pago'
            ], 422);
        }

        if ($servicio->estado === 'pagado') {
            return response()->json([
                'success' => false,
                'message' => '⚠️ El servicio ya está pagado'
            ], 422);
        }

        $servicio->update([
            'estado' => 'pagado',
            'metodo_pago' => $validated['metodo_pago'] ?? 'efectivo',
            'hora_fin' => now()
        ]);

        if ($servicio->deuda) {
            $servicio->deuda->update(['estado' => 'pagado']);
        }

        return response()->json([
            'success' => true,
            'message' => '✅ Pago registrado exitosamente',
            'estado' => 'pagado',
            'servicio_id' => $servicio->id
        ]);
    }
}