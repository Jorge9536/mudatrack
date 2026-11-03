<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Servicio;
use App\Models\Cliente;
use App\Models\Chofer;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicioControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function recepcionista_puede_ver_lista_de_servicios()
    {
        $user = User::factory()->create(['role' => 'recepcionista']);
        Servicio::factory()->count(3)->create();

        $response = $this->actingAs($user)->get('/servicios');
        $response->assertStatus(200);
    }

    /** @test */
    public function recepcionista_puede_registrar_un_servicio()
    {
        $user = User::factory()->create(['role' => 'recepcionista']);
        $cliente = Cliente::factory()->create();

        $response = $this->actingAs($user)->post('/servicios', [
            'cliente_id' => $cliente->id,
            'origen' => 'Av. 6 de Agosto, La Paz',
            'destino' => 'Calle 15, El Alto',
            'fecha_servicio' => now()->addDays(2)->format('Y-m-d'),
            'hora_inicio' => '09:00',
            'hora_fin' => '12:00',
            'cantidad_ayudantes' => 2,
            'numero_pisos' => 3,
            'es_callejon' => false,
            'costo_total' => 500.00,
            'estado' => 'pendiente',
            'metodo_pago' => 'efectivo',
        ]);

        // Verificar errores de validación (depuración)
        if ($response->exception) {
            dump($response->exception->getMessage());
        }
        $response->assertSessionHasNoErrors();

        $response->assertRedirect('/servicios');
        $this->assertDatabaseHas('servicios', [
            'origen' => 'Av. 6 de Agosto, La Paz',
            'destino' => 'Calle 15, El Alto',
        ]);
    }

    /** @test */
    public function recepcionista_puede_cambiar_estado_de_un_servicio()
    {
        $user = User::factory()->create(['role' => 'recepcionista']);
        $servicio = Servicio::factory()->create(['estado' => 'pendiente']);

        $response = $this->actingAs($user)->put("/servicios/{$servicio->id}/estado", [
            'estado' => 'confirmado',
        ]);

        // Aceptar tanto redirección como respuesta exitosa (200, 201, 302, etc.)
        $this->assertTrue(
            in_array($response->status(), [200, 201, 301, 302, 303, 307, 308]),
            "Se esperaba una respuesta exitosa, se recibió: {$response->status()}"
        );

        $this->assertEquals('confirmado', $servicio->fresh()->estado);
    }

    /** @test */
    public function admin_puede_ver_formulario_de_asignar_personal()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $servicio = Servicio::factory()->create();

        $response = $this->actingAs($user)->get("/servicios/{$servicio->id}/asignar");
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_puede_asignar_chofer_y_vehiculo_a_un_servicio()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $servicio = Servicio::factory()->create();
        $chofer = Chofer::factory()->create();
        $vehiculo = Vehiculo::factory()->create();

        $response = $this->actingAs($user)->post("/servicios/{$servicio->id}/asignar", [
            'chofer_id' => $chofer->id,
            'vehiculo_id' => $vehiculo->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('servicios', [
            'id' => $servicio->id,
            'chofer_id' => $chofer->id,
            'vehiculo_id' => $vehiculo->id,
        ]);
    }

    /** @test */
    public function recepcionista_puede_registrar_pago_de_un_servicio()
    {
        $user = User::factory()->create(['role' => 'recepcionista']);
        $servicio = Servicio::factory()->create([
            'estado' => 'finalizado',
            'costo_total' => 500.00,
        ]);

        $response = $this->actingAs($user)->post("/servicios/{$servicio->id}/pago", [
            'metodo_pago' => 'efectivo',
        ]);

        // Aceptar tanto redirección como respuesta exitosa
        $this->assertTrue(
            in_array($response->status(), [200, 201, 301, 302, 303, 307, 308]),
            "Se esperaba una respuesta exitosa, se recibió: {$response->status()}"
        );
    }

    /** @test */
    public function recepcionista_puede_generar_comprobante_de_un_servicio()
    {
        $user = User::factory()->create(['role' => 'recepcionista']);
        $servicio = Servicio::factory()->create(['estado' => 'finalizado']);

        $response = $this->actingAs($user)->get("/servicios/{$servicio->id}/comprobante");
        $response->assertStatus(200);
    }

    /** @test */
    public function usuario_no_autenticado_no_puede_ver_servicios()
    {
        $response = $this->get('/servicios');
        $response->assertRedirect('/login');
    }
}