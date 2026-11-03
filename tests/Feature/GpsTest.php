<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Servicio;
use App\Models\Chofer;
use App\Models\UbicacionGps;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GpsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cliente_puede_ver_seguimiento_publico_sin_login()
    {
        $servicio = Servicio::factory()->create();
        $token = $servicio->token_seguimiento;

        $response = $this->get("/seguimiento/{$token}");
        $response->assertStatus(200);
    }

    /** @test */
    public function seguimiento_publico_devuelve_404_con_token_invalido()
    {
        $response = $this->get('/seguimiento/token-invalido-12345');
        $response->assertStatus(404);
    }

    /** @test */
    public function usuario_autenticado_puede_ver_panel_de_gps()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user)->get('/gps');
        $response->assertStatus(200);
    }

    /** @test */
    public function usuario_autenticado_puede_ver_seguimiento_privado()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $servicio = Servicio::factory()->create(['estado' => 'en_progreso']);

        $response = $this->actingAs($user)->get("/gps/seguimiento/{$servicio->id}");
        $response->assertStatus(200);
    }

    /** @test */
    public function endpoint_publico_devuelve_404_si_servicio_no_tiene_chofer()
    {
        $servicio = Servicio::factory()->create([
            'estado' => 'en_progreso',
            'chofer_id' => null,
        ]);
        $token = $servicio->token_seguimiento;

        $response = $this->getJson("/seguimiento/{$token}/ubicacion");
        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
    }

    /** @test */
    public function endpoint_publico_devuelve_404_si_chofer_no_tiene_dispositivo()
    {
        $chofer = Chofer::factory()->create();
        $servicio = Servicio::factory()->create([
            'estado' => 'en_progreso',
            'chofer_id' => $chofer->id,
        ]);
        $token = $servicio->token_seguimiento;

        $response = $this->getJson("/seguimiento/{$token}/ubicacion");
        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
    }
}