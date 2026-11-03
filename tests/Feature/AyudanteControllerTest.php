<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Ayudante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AyudanteControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_puede_ver_lista_de_ayudantes()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Ayudante::factory()->count(3)->create();

        $response = $this->actingAs($user)->get('/ayudantes');
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_puede_registrar_un_ayudante()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->post('/ayudantes', [
            'nombre_completo' => 'Luis Quispe',
            'telefono' => '72345678',
            'disponible' => true,
        ]);

        $response->assertRedirect('/ayudantes');
        $this->assertDatabaseHas('ayudantes', ['telefono' => '72345678']);
    }

    /** @test */
    public function admin_puede_cambiar_disponibilidad_de_un_ayudante()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $ayudante = Ayudante::factory()->create(['disponible' => true]);

        $response = $this->actingAs($user)->post("/ayudantes/{$ayudante->id}/toggle-disponibilidad");

        $response->assertRedirect();
        $this->assertFalse($ayudante->fresh()->disponible);
    }

    /** @test */
    public function admin_puede_eliminar_un_ayudante()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $ayudante = Ayudante::factory()->create();

        $response = $this->actingAs($user)->delete("/ayudantes/{$ayudante->id}");

        $response->assertRedirect('/ayudantes');
        $this->assertDatabaseMissing('ayudantes', ['id' => $ayudante->id]);
    }
}