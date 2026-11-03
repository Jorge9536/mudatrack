<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehiculoControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_puede_ver_lista_de_vehiculos()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Vehiculo::factory()->count(3)->create();

        $response = $this->actingAs($user)->get('/vehiculos');
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_puede_registrar_un_vehiculo()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->post('/vehiculos', [
            'placa' => 'ABC-123',
            'marca' => 'Mitsubishi',
            'modelo' => 'NPR',
            'tipo' => '3ton',
            'capacidad_kg' => 3000,
            'disponible' => true,
        ]);

        $response->assertRedirect('/vehiculos');
        $this->assertDatabaseHas('vehiculos', ['placa' => 'ABC-123']);
    }

    /** @test */
    public function admin_puede_cambiar_disponibilidad_de_un_vehiculo()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $vehiculo = Vehiculo::factory()->create(['disponible' => true]);

        $response = $this->actingAs($user)->post("/vehiculos/{$vehiculo->id}/toggle-disponibilidad");

        $response->assertRedirect();
        $this->assertFalse($vehiculo->fresh()->disponible);
    }

    /** @test */
    public function admin_puede_eliminar_un_vehiculo()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $vehiculo = Vehiculo::factory()->create();

        $response = $this->actingAs($user)->delete("/vehiculos/{$vehiculo->id}");

        $response->assertRedirect('/vehiculos');
        $this->assertDatabaseMissing('vehiculos', ['id' => $vehiculo->id]);
    }
}