<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Servicio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeguridadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function recepcionista_no_puede_acceder_a_gestion_de_usuarios()
    {
        $user = User::factory()->create(['role' => 'recepcionista']);
        $response = $this->actingAs($user)->get('/users');
        $response->assertStatus(403);
    }

    /** @test */
    public function recepcionista_no_puede_acceder_a_configuracion_de_precios()
    {
        $user = User::factory()->create(['role' => 'recepcionista']);
        $response = $this->actingAs($user)->get('/configuracion/precios');
        $response->assertStatus(403);
    }

    /** @test */
    public function chofer_no_puede_acceder_a_reportes()
    {
        $user = User::factory()->create(['role' => 'chofer']);
        $response = $this->actingAs($user)->get('/reportes');
        $response->assertStatus(403);
    }

    /** @test */
    public function chofer_no_puede_acceder_a_gestion_de_vehiculos()
    {
        $user = User::factory()->create(['role' => 'chofer']);
        $response = $this->actingAs($user)->get('/vehiculos');
        $response->assertStatus(403);
    }

    /** @test */
    public function usuario_no_autenticado_no_puede_acceder_a_clientes()
    {
        $response = $this->get('/clientes');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function usuario_no_autenticado_no_puede_acceder_a_servicios()
    {
        $response = $this->get('/servicios');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function usuario_no_autenticado_no_puede_acceder_a_dashboard()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function token_de_seguimiento_es_unico()
    {
        $servicios = Servicio::factory()->count(10)->create();
        $tokens = $servicios->pluck('token_seguimiento')->toArray();
        $this->assertEquals(10, count(array_unique($tokens)));
    }

    /** @test */
    public function contrasena_esta_hasheada_en_base_de_datos()
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);
        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(password_verify('password123', $user->password));
    }

    /** @test */
    public function no_puede_registrar_cliente_con_sql_injection()
    {
        $user = User::factory()->create(['role' => 'recepcionista']);
        
        $response = $this->actingAs($user)->post('/clientes', [
            'nombre_completo' => "Robert'); DROP TABLE clientes;--",
            'telefono' => '78945612',
            'direccion' => 'Test',
        ]);

        // La tabla clientes debe seguir existiendo
        $this->assertDatabaseHas('clientes', ['telefono' => '78945612']);
    }
}