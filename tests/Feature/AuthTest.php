<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function usuario_puede_ver_formulario_de_login()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    /** @test */
    public function usuario_puede_iniciar_sesion_con_credenciales_validas()
    {
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function usuario_no_puede_iniciar_sesion_con_credenciales_invalidas()
    {
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /** @test */
    public function usuario_autenticado_puede_cerrar_sesion()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /** @test */
    public function ruta_raiz_redirige_al_dashboard()
    {
        $response = $this->get('/');
        $response->assertRedirect('/dashboard');
    }
}