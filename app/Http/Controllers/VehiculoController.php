<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class VehiculoController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $vehiculos = Vehiculo::orderBy('placa')->paginate(15);
        return view('vehiculos.index', compact('vehiculos'));
    }

    public function create()
    {
        return view('vehiculos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'placa' => 'required|string|max:10|unique:vehiculos',
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:50',
            'tipo' => 'required|in:3ton,6ton,chata',
            'capacidad_kg' => 'required|integer|min:1',
            'observaciones' => 'nullable|string'
        ]);

        $validated['disponible'] = true;
        Vehiculo::create($validated);

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehículo registrado exitosamente.');
    }

    public function show(Vehiculo $vehiculo)
    {
        $vehiculo->load('servicios');
        return view('vehiculos.show', compact('vehiculo'));
    }

    public function edit(Vehiculo $vehiculo)
    {
        return view('vehiculos.edit', compact('vehiculo'));
    }

    public function update(Request $request, Vehiculo $vehiculo)
    {
        $validated = $request->validate([
            'placa' => 'required|string|max:10|unique:vehiculos,placa,' . $vehiculo->id,
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:50',
            'tipo' => 'required|in:3ton,6ton,chata',
            'capacidad_kg' => 'required|integer|min:1',
            'disponible' => 'boolean',
            'observaciones' => 'nullable|string'
        ]);

        $vehiculo->update($validated);

        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehículo actualizado exitosamente.');
    }

    public function destroy(Vehiculo $vehiculo)
    {
        if ($vehiculo->servicios()->whereNotIn('estado', ['finalizado', 'cancelado'])->count() > 0) {
            return redirect()->route('vehiculos.index')
                ->with('error', 'No se puede eliminar el vehículo porque tiene servicios activos.');
        }

        $vehiculo->delete();
        return redirect()->route('vehiculos.index')
            ->with('success', 'Vehículo eliminado exitosamente.');
    }

    public function toggleDisponibilidad(Vehiculo $vehiculo)
    {
        $vehiculo->disponible = !$vehiculo->disponible;
        $vehiculo->save();

        return redirect()->route('vehiculos.index')
            ->with('success', 'Disponibilidad del vehículo actualizada.');
    }
}