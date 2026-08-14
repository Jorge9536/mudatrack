<?php

namespace App\Http\Controllers;

use App\Models\Ayudante;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class AyudanteController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $ayudantes = Ayudante::orderBy('nombre_completo')->paginate(15);
        return view('ayudantes.index', compact('ayudantes'));
    }

    public function create()
    {
        return view('ayudantes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'telefono' => 'required|string|max:20|unique:ayudantes',
            'disponible' => 'boolean'
        ]);

        Ayudante::create($validated);

        return redirect()->route('ayudantes.index')
            ->with('success', '✅ Ayudante registrado exitosamente.');
    }

    public function show(Ayudante $ayudante)
    {
        return view('ayudantes.show', compact('ayudante'));
    }

    public function edit(Ayudante $ayudante)
    {
        return view('ayudantes.edit', compact('ayudante'));
    }

    public function update(Request $request, Ayudante $ayudante)
    {
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'telefono' => 'required|string|max:20|unique:ayudantes,telefono,' . $ayudante->id,
            'disponible' => 'boolean'
        ]);

        $ayudante->update($validated);

        return redirect()->route('ayudantes.index')
            ->with('success', '✅ Ayudante actualizado exitosamente.');
    }

    public function destroy(Ayudante $ayudante)
    {
        $ayudante->delete();
        return redirect()->route('ayudantes.index')
            ->with('success', '✅ Ayudante eliminado exitosamente.');
    }

    public function toggleDisponibilidad(Ayudante $ayudante)
    {
        $ayudante->disponible = !$ayudante->disponible;
        $ayudante->save();

        return redirect()->route('ayudantes.index')
            ->with('success', '✅ Disponibilidad actualizada.');
    }
}