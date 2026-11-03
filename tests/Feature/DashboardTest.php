<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Servicio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function usuario_autenticado_puede_ver_dashboard()
    {
        $user = User::factory()->create(['role' => 'recepcionista']);
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }

    /** @test */
    public function dashboard_muestra_estadisticas_de_servicios()
    {
        $user = User::factory()->create(['role' => 'recepcionista']);
        Servicio::factory()->count(5)->create(['estado' => 'pendiente']);
        Servicio::factory()->count(3)->create(['estado' => 'finalizado']);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('totalServicios');
    }

    /** @test */
    public function admin_puede_ver_reportes()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user)->get('/reportes');
        $response->assertStatus(200);
    }

    /** @test */
    public function recepcionista_puede_ver_reportes()
    {
        $user = User::factory()->create(['role' => 'recepcionista']);
        $response = $this->actingAs($user)->get('/reportes');
        $response->assertStatus(200);
    }

    /** @test */
    public function chofer_no_puede_ver_reportes()
    {
        $user = User::factory()->create(['role' => 'chofer']);
        $response = $this->actingAs($user)->get('/reportes');
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_puede_ver_reportes_de_morosos()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user)->get('/reportes/morosos');
        $response->assertStatus(200);
    }
}