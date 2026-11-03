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
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class ServicioController extends BaseController
{
    protected $cotizacionService;

    public function __construct(CotizacionService $cotizacionService)
    {
        $this->cotizacionService = $cotizacionService;
        $this->middleware('auth');
    }

  
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Servicio::with(['cliente', 'chofer', 'vehiculo']);

        if ($user->isChofer() && $user->chofer) {
            $query->where('chofer_id', $user->chofer->id);
        }

        if ($user->isAyudante() && $user->ayudante) {
            $query->whereHas('ayudantes', function ($q) use ($user) {
                $q->where('ayudante_id', $user->ayudante->id);
            });
        }

    
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search, $user) {
              
                $q->whereHas('cliente', function ($q2) use ($search) {
                    $q2->where('nombre_completo', 'ILIKE', "%{$search}%")
                       ->orWhere('telefono', 'ILIKE', "%{$search}%");
                })
               
                ->orWhere('origen', 'ILIKE', "%{$search}%")
                ->orWhere('destino', 'ILIKE', "%{$search}%");

               
                if (is_numeric($search)) {
                    $q->orWhere('id', (int) $search);
                }

    
                if ($user->isAdmin() || $user->isRecepcionista()) {
                    $q->orWhereHas('chofer', function ($q2) use ($search) {
                        $q2->where('nombre_completo', 'ILIKE', "%{$search}%");
                    });
                }
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }


        if ($request->filled('estado_pago')) {
            $query->where('estado_pago', $request->estado_pago);
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_servicio', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_servicio', '<=', $request->fecha_fin);
        }

        $servicios = $query->orderBy('id', 'desc')->paginate(15);

        return view('servicios.index', compact('servicios'));
    }

    public function create(Request $request)
    {
        $clientes  = Cliente::all();
        $choferes  = Chofer::where('disponible', true)->get();
        $vehiculos = Vehiculo::where('disponible', true)->get();
        $ayudantes = Ayudante::where('disponible', true)->get();

        $configPrecios = ConfiguracionPrecio::getConfig();

        $fechaSeleccionada      = $request->fecha ?? null;
        $horaInicioSeleccionada = $request->hora_inicio ?? null;
        $horaFinSeleccionada    = $request->hora_fin ?? null;

        return view('servicios.create', compact(
            'clientes',
            'choferes',
            'vehiculos',
            'ayudantes',
            'configPrecios',
            'fechaSeleccionada',
            'horaInicioSeleccionada',
            'horaFinSeleccionada'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id'         => ['required', 'exists:clientes,id'],
            'origen'             => ['required', 'string', 'max:255', 'regex:/^[\pL0-9\s\.,#\'\-\(\)\/]+$/u'],
            'destino'            => ['required', 'string', 'max:255', 'regex:/^[\pL0-9\s\.,#\'\-\(\)\/]+$/u'],
            'fecha_servicio'     => ['required', 'date'],
            'hora_inicio'        => ['nullable', 'date_format:H:i'],
            'hora_fin'           => ['nullable', 'date_format:H:i', 'after:hora_inicio'],
            'cantidad_ayudantes' => ['required', 'integer', 'min:0'],
            'numero_pisos'       => ['required', 'integer', 'min:1'],
            'es_callejon'        => ['boolean'],
            'distancia_km'       => ['nullable', 'numeric', 'min:0'],
            'bienes'             => ['array'],
            'bienes.*.nombre'    => ['required', 'string', 'max:255'],
            'bienes.*.cantidad'  => ['required', 'integer', 'min:1'],
            'bienes.*.descripcion' => ['nullable', 'string', 'max:500'],
            'observaciones'      => ['nullable', 'string', 'max:1000'],
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
            'zona'         => $zona,
            'ayudantes'    => $validated['cantidad_ayudantes'],
            'pisos'        => $validated['numero_pisos'],
            'es_callejon'  => $validated['es_callejon'] ?? false,
            'distancia_km' => $validated['distancia_km'] ?? 0,
        ]);

        $servicio = Servicio::create([
            'cliente_id'         => $validated['cliente_id'],
            'origen'             => $validated['origen'],
            'destino'            => $validated['destino'],
            'fecha_servicio'     => $validated['fecha_servicio'],
            'hora_inicio'        => $validated['hora_inicio'] ?? null,
            'hora_fin'           => $validated['hora_fin'] ?? null,
            'cantidad_ayudantes' => $validated['cantidad_ayudantes'],
            'numero_pisos'       => $validated['numero_pisos'],
            'es_callejon'        => $validated['es_callejon'] ?? false,
            'distancia_km'       => $validated['distancia_km'] ?? 0,
            'costo_total'        => $costo,
            'estado'             => 'pendiente',
            'estado_pago'        => 'pendiente',
            'observaciones'      => $validated['observaciones'] ?? null,
        ]);

        if (isset($validated['bienes'])) {
            foreach ($validated['bienes'] as $bien) {
                Bien::create([
                    'servicio_id' => $servicio->id,
                    'nombre'      => $bien['nombre'],
                    'cantidad'    => $bien['cantidad'],
                    'descripcion' => $bien['descripcion'] ?? null,
                ]);
            }
        }

        return redirect()->route('servicios.index')
            ->with('success', 'Servicio creado exitosamente. Costo total: ' .
                number_format($costo, 2) . ' Bs');
    }

    public function show(Servicio $servicio)
    {
        $user = auth()->user();

        if ($user->isChofer() && $user->chofer) {
            if ($servicio->chofer_id !== $user->chofer->id) {
                abort(403, 'No puedes ver servicios que no te pertenecen.');
            }
        }

        if ($user->isAyudante() && $user->ayudante) {
            if (!$servicio->ayudantes->contains('id', $user->ayudante->id)) {
                abort(403, 'No puedes ver servicios donde no estás asignado.');
            }
        }

        $servicio->load(['cliente', 'bienes', 'chofer', 'vehiculo', 'deuda', 'ayudantes']);
        return view('servicios.show', compact('servicio'));
    }
    public function updateStatus(Request $request, Servicio $servicio)
    {
        $user = auth()->user();

        if ($user->hasRole('chofer')) {
            if (!$user->chofer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tu usuario no tiene un chofer asociado. Contacta al administrador.',
                ], 403);
            }

            if ($servicio->chofer_id !== $user->chofer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes modificar servicios que no te pertenecen.',
                ], 403);
            }

            $estadosPermitidos = ['en_progreso', 'finalizado'];
            if (!in_array($request->estado, $estadosPermitidos, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo puedes marcar como "En Progreso" o "Finalizado".',
                ], 403);
            }
        }

        $validated = $request->validate([
            'estado' => 'required|in:' . implode(',', Servicio::ESTADOS),
        ]);

        if (!$servicio->puedeTransicionar($validated['estado'])) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede cambiar de ' . $servicio->estado . ' a ' . $validated['estado'],
            ], 422);
        }

        $estadoAnterior = $servicio->estado;

        if ($validated['estado'] === 'finalizado' && !$servicio->estaPagado()) {
            $servicio->update(['estado' => 'finalizado']);

            if (!$servicio->deuda) {
                Deuda::create([
                    'cliente_id'        => $servicio->cliente_id,
                    'servicio_id'       => $servicio->id,
                    'monto'             => $servicio->costo_total,
                    'fecha_vencimiento' => now()->addDays(1),
                    'estado'            => 'pendiente',
                    'observaciones'     => 'Servicio finalizado sin pago',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Servicio finalizado. Pendiente de pago.',
                'estado'  => 'finalizado',
            ]);
        }

        $servicio->update(['estado' => $validated['estado']]);

        if ($validated['estado'] === 'en_progreso'
            && $estadoAnterior !== 'en_progreso'
            && $servicio->cliente
            && $servicio->cliente->telefono) {

            try {
                $this->enviarSeguimiento($servicio);
                Log::info("Seguimiento WhatsApp enviado automáticamente al servicio #{$servicio->id}");
            } catch (\Exception $e) {
                Log::warning('No se pudo enviar seguimiento automático', [
                    'servicio_id' => $servicio->id,
                    'error'       => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'estado'  => $servicio->estado,
        ]);
    }

    public function showAsignarForm(Servicio $servicio)
    {
        $choferes  = Chofer::where('disponible', true)->get();
        $vehiculos = Vehiculo::where('disponible', true)->get();
        $ayudantes = Ayudante::where('disponible', true)->get();

        return view('servicios.asignar', compact('servicio', 'choferes', 'vehiculos', 'ayudantes'));
    }

    public function assignPersonal(Request $request, Servicio $servicio)
    {
        $validated = $request->validate([
            'chofer_id'   => 'required|exists:choferes,id',
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'ayudantes'   => 'array',
            'ayudantes.*' => 'exists:ayudantes,id',
        ]);

        $fecha      = $servicio->fecha_servicio->format('Y-m-d');
        $horaInicio = $servicio->hora_inicio ? \Carbon\Carbon::parse($servicio->hora_inicio)->format('H:i') : '00:00';
        $horaFin    = $servicio->hora_fin    ? \Carbon\Carbon::parse($servicio->hora_fin)->format('H:i')    : '23:59';

        $vehiculo = Vehiculo::find($validated['vehiculo_id']);
        if (!$vehiculo->isAvailableForEdit($fecha, $horaInicio, $horaFin, $servicio->id)) {
            return redirect()->back()
                ->with('error', '❌ El vehículo ' . $vehiculo->placa . ' ya está ocupado en ese horario por otro servicio.')
                ->withInput();
        }

        $chofer = Chofer::find($validated['chofer_id']);
        if (!$chofer->isAvailableForEdit($fecha, $horaInicio, $horaFin, $servicio->id)) {
            return redirect()->back()
                ->with('error', '❌ El chofer ' . $chofer->nombre_completo . ' ya está ocupado en ese horario por otro servicio.')
                ->withInput();
        }

        if (isset($validated['ayudantes']) && !empty($validated['ayudantes'])) {
            foreach ($validated['ayudantes'] as $ayudanteId) {
                $ayudante = Ayudante::find($ayudanteId);
                if (!$ayudante->isAvailableForEdit($fecha, $horaInicio, $horaFin, $servicio->id)) {
                    return redirect()->back()
                        ->with('error', '❌ El ayudante ' . $ayudante->nombre_completo . ' ya está ocupado en ese horario por otro servicio.')
                        ->withInput();
                }
            }
        }

        $servicio->update([
            'chofer_id'   => $validated['chofer_id'],
            'vehiculo_id' => $validated['vehiculo_id'],
            'estado'      => 'confirmado',
        ]);

        if (isset($validated['ayudantes'])) {
            $servicio->ayudantes()->sync($validated['ayudantes']);
        } else {
            $servicio->ayudantes()->sync([]);
        }

        return redirect()->route('servicios.show', $servicio)
            ->with('success', '✅ Personal asignado correctamente.');
    }

    public function generarComprobante(Servicio $servicio)
    {
        $servicio->load(['cliente', 'bienes', 'chofer', 'vehiculo', 'ayudantes']);

        $pdf = Pdf::loadView('pdf.comprobante', compact('servicio'));

        return $pdf->download('comprobante-' . $servicio->id . '.pdf');
    }

    public function registrarPago(Request $request, Servicio $servicio)
    {
        $validated = $request->validate([
            'metodo_pago' => 'required|in:efectivo,qr,transferencia',
            'monto'       => 'nullable|numeric|min:0',
        ]);

        if ($servicio->estado === 'cancelado') {
            return response()->json([
                'success' => false,
                'message' => '❌ El servicio está cancelado, no se puede registrar pago',
            ], 422);
        }

        if ($servicio->estado_pago === 'pagado') {
            return response()->json([
                'success' => false,
                'message' => '⚠️ El servicio ya está pagado',
            ], 422);
        }

        $servicio->update([
            'estado_pago' => 'pagado',
            'metodo_pago' => $validated['metodo_pago'] ?? 'efectivo',
        ]);

        if ($servicio->deuda) {
            $servicio->deuda->update(['estado' => 'pagado']);
        }

        return response()->json([
            'success'     => true,
            'message'     => '✅ Pago registrado exitosamente',
            'estado'      => $servicio->estado,
            'estado_pago' => 'pagado',
            'servicio_id' => $servicio->id,
        ]);
    }

    public function getAsignarFormJson(Servicio $servicio)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isRecepcionista()) {
            return response()->json(['error' => 'No tienes permisos'], 403);
        }

        $choferes  = Chofer::where('disponible', true)->get();
        $vehiculos = Vehiculo::where('disponible', true)->get();
        $ayudantes = Ayudante::where('disponible', true)->get();

        $fecha      = $servicio->fecha_servicio->format('Y-m-d');
        $horaInicio = $servicio->hora_inicio ? \Carbon\Carbon::parse($servicio->hora_inicio)->format('H:i') : '00:00';
        $horaFin    = $servicio->hora_fin    ? \Carbon\Carbon::parse($servicio->hora_fin)->format('H:i')    : '23:59';

        $vehiculosDisponibles = $vehiculos->filter(fn($v) => $v->isAvailable($fecha, $horaInicio, $horaFin));
        $choferesDisponibles  = $choferes->filter(fn($c) => $c->isAvailable($fecha, $horaInicio, $horaFin));
        $ayudantesDisponibles = $ayudantes->filter(fn($a) => $a->isAvailable($fecha, $horaInicio, $horaFin));

        $html = view('servicios.asignar-rapido', compact(
            'servicio',
            'vehiculosDisponibles',
            'choferesDisponibles',
            'ayudantesDisponibles'
        ))->render();

        return response()->json(['html' => $html]);
    }

    public function enviarWhatsApp(Servicio $servicio)
    {
        try {
            if (!$servicio->cliente || !$servicio->cliente->telefono) {
                return response()->json([
                    'success' => false,
                    'message' => 'El cliente no tiene teléfono registrado',
                ]);
            }

            $servicio->load(['cliente', 'bienes', 'chofer', 'vehiculo', 'ayudantes']);

            $pdf = Pdf::loadView('pdf.comprobante', compact('servicio'));

            $nombreArchivo = 'comprobante-' . $servicio->id . '.pdf';
            $rutaPDF       = storage_path('app/temp/' . $nombreArchivo);

            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            $pdf->save($rutaPDF);

            $phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
            $accessToken   = env('WHATSAPP_ACCESS_TOKEN');

            $responseMedia = Http::withToken($accessToken)
                ->attach('file', file_get_contents($rutaPDF), $nombreArchivo)
                ->post("https://graph.facebook.com/v25.0/{$phoneNumberId}/media", [
                    'messaging_product' => 'whatsapp',
                ]);

            if (!$responseMedia->successful()) {
                Log::error('Error subiendo PDF a Meta:', $responseMedia->json());
                if (file_exists($rutaPDF)) unlink($rutaPDF);

                return response()->json([
                    'success' => false,
                    'message' => 'Error al subir el PDF a WhatsApp: ' .
                        ($responseMedia->json()['error']['message'] ?? 'Desconocido'),
                ]);
            }

            $mediaId = $responseMedia->json()['id'];

            $telefono = $this->formatearTelefonoBolivia($servicio->cliente->telefono);

            $data = [
                'messaging_product' => 'whatsapp',
                'to'                => $telefono,
                'type'              => 'template',
                'template'          => [
                    'name'     => 'comprobante_pago',
                    'language' => ['code' => 'es'],
                    'components' => [
                        [
                            'type' => 'header',
                            'parameters' => [
                                [
                                    'type' => 'document',
                                    'document' => [
                                        'id'       => $mediaId,
                                        'filename' => 'Comprobante-' . Str::slug($servicio->cliente->nombre_completo) . '.pdf',
                                    ],
                                ],
                            ],
                        ],
                        [
                            'type' => 'body',
                            'parameters' => [
                                ['type' => 'text', 'text' => $servicio->cliente->nombre_completo],
                                ['type' => 'text', 'text' => number_format($servicio->costo_total, 2)],
                                ['type' => 'text', 'text' => $servicio->fecha_servicio->format('d/m/Y')],
                            ],
                        ],
                    ],
                ],
            ];

            $response = Http::withToken($accessToken)
                ->post("https://graph.facebook.com/v25.0/{$phoneNumberId}/messages", $data);

            if (file_exists($rutaPDF)) {
                unlink($rutaPDF);
            }

            if (!$response->successful()) {
                Log::error('Error enviando plantilla WhatsApp:', $response->json());
                return response()->json([
                    'success' => false,
                    'message' => 'Error al enviar: ' .
                        ($response->json()['error']['message'] ?? 'Desconocido'),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Comprobante enviado a ' . $servicio->cliente->nombre_completo,
            ]);

        } catch (\Exception $e) {
            Log::error('Excepción enviando WhatsApp:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    public function enviarSeguimiento(Servicio $servicio)
    {
        try {
            if (!$servicio->cliente || !$servicio->cliente->telefono) {
                return response()->json([
                    'success' => false,
                    'message' => 'El cliente no tiene teléfono registrado',
                ], 422);
            }

            if (empty($servicio->token_seguimiento)) {
                $servicio->update([
                    'token_seguimiento' => Str::random(48),
                ]);
            }

            $nombreCliente   = $servicio->cliente->nombre_completo;
            $linkSeguimiento = route('seguimiento.publico', $servicio->token_seguimiento);
            $telefono        = $this->formatearTelefonoBolivia($servicio->cliente->telefono);

            $phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
            $accessToken   = env('WHATSAPP_ACCESS_TOKEN');

            $data = [
                'messaging_product' => 'whatsapp',
                'to'                => $telefono,
                'type'              => 'template',
                'template'          => [
                    'name'     => 'seguimiento_mudanza_en_vivo',
                    'language' => ['code' => 'es'],
                    'components' => [
                        [
                            'type'       => 'body',
                            'parameters' => [
                                ['type' => 'text', 'text' => $nombreCliente],
                                ['type' => 'text', 'text' => $linkSeguimiento],
                            ],
                        ],
                    ],
                ],
            ];

            $response = Http::withToken($accessToken)
                ->post("https://graph.facebook.com/v25.0/{$phoneNumberId}/messages", $data);

            if (!$response->successful()) {
                Log::error('Error enviando seguimiento WhatsApp:', $response->json());

                return response()->json([
                    'success' => false,
                    'message' => 'Error al enviar: ' .
                        ($response->json()['error']['message'] ?? 'Desconocido'),
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Seguimiento enviado a ' . $nombreCliente,
                'link'    => $linkSeguimiento,
            ]);

        } catch (\Exception $e) {
            Log::error('Excepción enviando seguimiento:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function formatearTelefonoBolivia($telefono)
    {
        $telefono = preg_replace('/[^0-9]/', '', $telefono);

        if (str_starts_with($telefono, '591')) {
            return $telefono;
        }

        if (strlen($telefono) === 8) {
            return '591' . $telefono;
        }

        if (strlen($telefono) === 7) {
            return '591' . $telefono;
        }

        if (strlen($telefono) === 9 && str_starts_with($telefono, '0')) {
            return '591' . substr($telefono, 1);
        }

        return $telefono;
    }
}