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
            'nombre_completo' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'telefono' => ['required', 'string', 'max:20', 'regex:/^[0-9\+\-\s\(\)]+$/', 'unique:clientes'],
            'direccion' => ['nullable', 'string', 'max:500'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'foto_casa' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
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
            'nombre_completo' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'telefono' => ['required', 'string', 'max:20', 'regex:/^[0-9\+\-\s\(\)]+$/', 'unique:clientes,telefono,' . $cliente->id],
            'direccion' => ['nullable', 'string', 'max:500'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'foto_casa' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
            'bloqueado' => ['boolean'],
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