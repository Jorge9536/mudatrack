<?php

namespace App\Http\Controllers;

use App\Models\Chofer;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class ChoferController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $choferes = Chofer::orderBy('nombre_completo')->paginate(15);
        return view('choferes.index', compact('choferes'));
    }

    public function create()
    {
        return view('choferes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_completo' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'telefono' => ['required', 'string', 'max:20', 'regex:/^[0-9\+\-\s\(\)]+$/', 'unique:choferes'],
            'licencia' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9\-]+$/', 'unique:choferes'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['disponible'] = true;
        Chofer::create($validated);

        return redirect()->route('choferes.index')
            ->with('success', 'Chofer registrado exitosamente.');
    }

    public function show(Chofer $chofer)
    {
        $chofer->load('servicios');
        return view('choferes.show', compact('chofer'));
    }

    public function edit(Chofer $chofer)
    {
        return view('choferes.edit', compact('chofer'));
    }

    public function update(Request $request, Chofer $chofer)
    {
        $validated = $request->validate([
            'nombre_completo' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'telefono' => ['required', 'string', 'max:20', 'regex:/^[0-9\+\-\s\(\)]+$/', 'unique:choferes,telefono,' . $chofer->id],
            'licencia' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9\-]+$/', 'unique:choferes,licencia,' . $chofer->id],
            'disponible' => ['boolean'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ]);

        $chofer->update($validated);

        return redirect()->route('choferes.index')
            ->with('success', 'Chofer actualizado exitosamente.');
    }

    public function destroy(Chofer $chofer)
    {
        if ($chofer->servicios()->whereNotIn('estado', ['finalizado', 'cancelado'])->count() > 0) {
            return redirect()->route('choferes.index')
                ->with('error', 'No se puede eliminar el chofer porque tiene servicios activos.');
        }

        $chofer->delete();
        return redirect()->route('choferes.index')
            ->with('success', 'Chofer eliminado exitosamente.');
    }

    public function panel()
    {
        $chofer = Chofer::first();
        $servicios = Servicio::where('chofer_id', $chofer?->id)
            ->whereIn('estado', ['confirmado', 'en_progreso'])
            ->with('cliente')
            ->get();

        return view('choferes.panel', compact('servicios'));
    }
}