<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class ClienteController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $clientes = Cliente::orderBy('created_at', 'desc')->paginate(15);
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'telefono' => 'required|string|max:20|unique:clientes',
            'direccion' => 'nullable|string',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'foto_casa' => 'nullable|string',
            'observaciones' => 'nullable|string'
        ]);

        $cliente = Cliente::create($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente registrado exitosamente.');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load(['servicios', 'deudas']);
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'telefono' => 'required|string|max:20|unique:clientes,telefono,' . $cliente->id,
            'direccion' => 'nullable|string',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'foto_casa' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'bloqueado' => 'boolean'
        ]);

        $cliente->update($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    public function destroy(Cliente $cliente)
    {
        if ($cliente->servicios()->count() > 0) {
            return redirect()->route('clientes.index')
                ->with('error', 'No se puede eliminar el cliente porque tiene servicios asociados.');
        }

        $cliente->delete();
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }

    public function toggleBloqueo(Cliente $cliente)
    {
        $cliente->bloqueado = !$cliente->bloqueado;
        $cliente->save();

        return redirect()->route('clientes.index')
            ->with('success', 'Estado del cliente actualizado.');
    }
}