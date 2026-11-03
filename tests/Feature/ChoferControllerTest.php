<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Chofer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChoferControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_puede_ver_lista_de_choferes()
    {
        $user = User::factory()->create(['role' => 'admin']);
        Chofer::factory()->count(3)->create();

        $response = $this->actingAs($user)->get('/choferes');
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_puede_registrar_un_chofer()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->post('/choferes', [
            'nombre_completo' => 'Carlos Mamani',
            'telefono' => '71234567',
            'licencia' => 'ABC12345',
            'disponible' => true,
        ]);

        $response->assertRedirect('/choferes');
        $this->assertDatabaseHas('choferes', ['licencia' => 'ABC12345']);
    }

    /** @test */
    public function admin_puede_editar_un_chofer()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $chofer = Chofer::factory()->create();

        $response = $this->actingAs($user)->put("/choferes/{$chofer->id}", [
            'nombre_completo' => 'Nombre Editado',
            'telefono' => $chofer->telefono,
            'licencia' => $chofer->licencia,
            'disponible' => false,
        ]);

        $response->assertRedirect('/choferes');
        $this->assertDatabaseHas('choferes', [
            'id' => $chofer->id,
            'nombre_completo' => 'Nombre Editado',
        ]);
    }

    /** @test */
    public function admin_puede_eliminar_un_chofer()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $chofer = Chofer::factory()->create();

        $response = $this->actingAs($user)->delete("/choferes/{$chofer->id}");

        $response->assertRedirect('/choferes');
        $this->assertDatabaseMissing('choferes', ['id' => $chofer->id]);
    }

    /** @test */
    public function recepcionista_no_puede_ver_choferes()
    {
        $user = User::factory()->create(['role' => 'recepcionista']);
        $response = $this->actingAs($user)->get('/choferes');
        $response->assertStatus(403);
    }
}