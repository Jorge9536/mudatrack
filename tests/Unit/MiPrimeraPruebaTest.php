<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\Chofer;
use App\Models\Ayudante;
use App\Models\Servicio;
use App\Models\Bien;
use App\Models\Deuda;
use App\Models\UbicacionGps;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;

class MiPrimeraPruebaTest extends TestCase
{
    use RefreshDatabase;

    /** ========== PRUEBAS DE USUARIOS ========== */
    
    public function test_usuario_se_puede_crear_correctamente()
    {
        $user = User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan@test.com',
            'password' => bcrypt('12345678'),
            'role' => 'admin'
        ]);

        $this->assertEquals('Juan Pérez', $user->name);
        $this->assertEquals('juan@test.com', $user->email);
        $this->assertEquals('admin', $user->role);
        $this->assertDatabaseHas('users', ['email' => 'juan@test.com']);
    }

    public function test_email_del_usuario_debe_ser_unico()
    {
        User::create([
            'name' => 'Usuario 1',
            'email' => 'duplicado@test.com',
            'password' => bcrypt('12345678')
        ]);

        $this->expectException(QueryException::class);
        
        User::create([
            'name' => 'Usuario 2',
            'email' => 'duplicado@test.com',
            'password' => bcrypt('12345678')
        ]);
    }

    /** ========== PRUEBAS DE CLIENTES ========== */
    
    public function test_cliente_se_puede_crear_correctamente()
    {
        $cliente = Cliente::create([
            'nombre_completo' => 'María López',
            'telefono' => '5551234',
            'direccion' => 'Av. 6 de Agosto, La Paz',
            'latitud' => -16.5000,
            'longitud' => -68.1500,
            'bloqueado' => false,
        ]);

        $this->assertEquals('María López', $cliente->nombre_completo);
        $this->assertEquals('5551234', $cliente->telefono);
        $this->assertFalse($cliente->bloqueado);
        $this->assertDatabaseHas('clientes', ['nombre_completo' => 'María López']);
    }

    public function test_cliente_puede_ser_bloqueado()
    {
        $cliente = Cliente::create([
            'nombre_completo' => 'Cliente Test',
            'telefono' => '5550000',
            'direccion' => 'Calle Test',
            'latitud' => -16.5000,
            'longitud' => -68.1500,
            'bloqueado' => false
        ]);

        $cliente->bloqueado = true;
        $cliente->save();

        $this->assertTrue($cliente->bloqueado);
    }

    public function test_cliente_tiene_servicios()
    {
        $cliente = Cliente::factory()->create();
        Servicio::factory()->count(3)->create(['cliente_id' => $cliente->id]);
        $this->assertEquals(3, $cliente->servicios->count());
    }

    public function test_cliente_tiene_deudas()
    {
        $cliente = Cliente::factory()->create();
        Deuda::factory()->count(2)->create(['cliente_id' => $cliente->id]);
        $this->assertEquals(2, $cliente->deudas->count());
    }

    /** ========== PRUEBAS DE VEHÍCULOS ========== */
    
    public function test_vehiculo_se_puede_crear_correctamente()
    {
        $vehiculo = Vehiculo::create([
            'placa' => 'ABC-123',
            'marca' => 'Toyota',
            'modelo' => 'Hilux',
            'tipo' => 'chata',
            'capacidad_kg' => 1500,
            'disponible' => true
        ]);

        $this->assertEquals('ABC-123', $vehiculo->placa);
        $this->assertEquals('Toyota', $vehiculo->marca);
        $this->assertEquals('chata', $vehiculo->tipo);
        $this->assertEquals(1500, $vehiculo->capacidad_kg);
        $this->assertTrue($vehiculo->disponible);
        $this->assertDatabaseHas('vehiculos', ['placa' => 'ABC-123']);
    }

    public function test_vehiculo_puede_cambiar_disponibilidad()
    {
        $vehiculo = Vehiculo::create([
            'placa' => 'XYZ-789',
            'marca' => 'Ford',
            'modelo' => 'F-150',
            'tipo' => '6ton',
            'capacidad_kg' => 6000,
            'disponible' => true
        ]);

        $vehiculo->disponible = false;
        $vehiculo->save();
        $this->assertFalse($vehiculo->disponible);

        $vehiculo->disponible = true;
        $vehiculo->save();
        $this->assertTrue($vehiculo->disponible);
    }

    public function test_placa_del_vehiculo_debe_ser_unica()
    {
        Vehiculo::create([
            'placa' => 'AAA-111',
            'marca' => 'Nissan',
            'modelo' => 'D-Max',
            'tipo' => '3ton',
            'capacidad_kg' => 3000
        ]);

        $this->expectException(QueryException::class);
        
        Vehiculo::create([
            'placa' => 'AAA-111',
            'marca' => 'Chevrolet',
            'modelo' => 'NPR',
            'tipo' => '6ton',
            'capacidad_kg' => 6000
        ]);
    }

    /** ========== PRUEBAS DE CHOFERES ========== */
    
    public function test_chofer_se_puede_crear_correctamente()
    {
        $chofer = Chofer::create([
            'nombre_completo' => 'Carlos Pérez',
            'telefono' => '5555678',
            'licencia' => '12345',
            'disponible' => true
        ]);

        $this->assertEquals('Carlos Pérez', $chofer->nombre_completo);
        $this->assertEquals('12345', $chofer->licencia);
        $this->assertTrue($chofer->disponible);
        $this->assertDatabaseHas('choferes', ['nombre_completo' => 'Carlos Pérez']);
    }

    public function test_chofer_puede_cambiar_estado()
    {
        $chofer = Chofer::create([
            'nombre_completo' => 'Chofer Test',
            'telefono' => '5559999',
            'licencia' => '99999',
            'disponible' => true
        ]);

        $chofer->disponible = false;
        $chofer->save();
        $this->assertFalse($chofer->disponible);
    }

    public function test_chofer_tiene_servicios()
    {
        $chofer = Chofer::factory()->create();
        Servicio::factory()->count(2)->create(['chofer_id' => $chofer->id]);
        $this->assertEquals(2, $chofer->servicios->count());
    }

    /** ========== PRUEBAS DE AYUDANTES ========== */
    
    public function test_ayudante_se_puede_crear_correctamente()
    {
        $ayudante = Ayudante::create([
            'nombre_completo' => 'Pedro Ayudante',
            'telefono' => '5557777',
            'disponible' => true
        ]);

        $this->assertEquals('Pedro Ayudante', $ayudante->nombre_completo);
        $this->assertEquals('5557777', $ayudante->telefono);
        $this->assertTrue($ayudante->disponible);
        $this->assertDatabaseHas('ayudantes', ['nombre_completo' => 'Pedro Ayudante']);
    }

    public function test_ayudante_puede_cambiar_estado()
    {
        $ayudante = Ayudante::create([
            'nombre_completo' => 'Ayudante Test',
            'telefono' => '5558888',
            'disponible' => true
        ]);

        $ayudante->disponible = false;
        $ayudante->save();
        $this->assertFalse($ayudante->disponible);
    }

    /** ========== PRUEBAS DE SERVICIOS (CORREGIDAS CON ESTADOS VÁLIDOS) ========== */
    
    public function test_servicio_se_puede_crear_correctamente()
    {
        $cliente = Cliente::factory()->create();
        $vehiculo = Vehiculo::factory()->create();
        $chofer = Chofer::factory()->create();

        $servicio = Servicio::create([
            'cliente_id' => $cliente->id,
            'vehiculo_id' => $vehiculo->id,
            'chofer_id' => $chofer->id,
            'origen' => 'Av. 6 de Agosto, La Paz',
            'destino' => 'Calle 15, El Alto',
            'fecha_servicio' => now()->addDays(2),
            'cantidad_ayudantes' => 2,
            'numero_pisos' => 3,
            'es_callejon' => false,
            'costo_total' => 500.00,
            'estado' => 'pendiente', // Estado válido
            'metodo_pago' => 'efectivo' // Método de pago válido
        ]);

        $this->assertEquals('pendiente', $servicio->estado);
        $this->assertEquals(500.00, $servicio->costo_total);
        $this->assertEquals(2, $servicio->cantidad_ayudantes);
        $this->assertDatabaseHas('servicios', ['costo_total' => 500.00]);
    }

    public function test_servicio_puede_cambiar_estado()
    {
        $cliente = Cliente::factory()->create();
        $vehiculo = Vehiculo::factory()->create();
        $chofer = Chofer::factory()->create();

        $servicio = Servicio::create([
            'cliente_id' => $cliente->id,
            'vehiculo_id' => $vehiculo->id,
            'chofer_id' => $chofer->id,
            'origen' => 'Origen',
            'destino' => 'Destino',
            'fecha_servicio' => now(),
            'cantidad_ayudantes' => 1,
            'numero_pisos' => 1,
            'es_callejon' => false,
            'costo_total' => 500.00,
            'estado' => 'pendiente', // Estado válido
            'metodo_pago' => 'efectivo' // Método de pago válido
        ]);

        // Cambiar a otro estado válido (según tu CHECK constraint)
        $servicio->estado = 'confirmado';
        $servicio->save();

        $this->assertEquals('confirmado', $servicio->estado);
    }

    public function test_servicio_puede_cambiar_metodo_pago()
    {
        $cliente = Cliente::factory()->create();
        $vehiculo = Vehiculo::factory()->create();
        $chofer = Chofer::factory()->create();

        $servicio = Servicio::create([
            'cliente_id' => $cliente->id,
            'vehiculo_id' => $vehiculo->id,
            'chofer_id' => $chofer->id,
            'origen' => 'Origen',
            'destino' => 'Destino',
            'fecha_servicio' => now(),
            'cantidad_ayudantes' => 1,
            'numero_pisos' => 1,
            'es_callejon' => false,
            'costo_total' => 500.00,
            'estado' => 'pendiente',
            'metodo_pago' => 'efectivo' // Método válido
        ]);

        // Cambiar a otro método válido
        $servicio->metodo_pago = 'qr';
        $servicio->save();

        $this->assertEquals('qr', $servicio->metodo_pago);
    }

    public function test_servicio_tiene_bienes()
    {
        $servicio = Servicio::factory()->create();
        Bien::factory()->count(3)->create(['servicio_id' => $servicio->id]);
        $this->assertEquals(3, $servicio->bienes->count());
    }

    public function test_servicio_tiene_ubicaciones_gps()
    {
        $servicio = Servicio::factory()->create();
        UbicacionGps::factory()->count(5)->create(['servicio_id' => $servicio->id]);
        $this->assertEquals(5, $servicio->ubicacionesGps->count());
    }

    /** ========== PRUEBAS CON FACTORIES ========== */
    
    public function test_se_pueden_crear_multiples_clientes_con_factory()
    {
        Cliente::factory()->count(5)->create();
        $this->assertEquals(5, Cliente::count());
    }

    public function test_se_pueden_crear_multiples_vehiculos_con_factory()
    {
        Vehiculo::factory()->count(3)->create();
        $this->assertEquals(3, Vehiculo::count());
    }

    public function test_se_pueden_crear_multiples_choferes_con_factory()
    {
        Chofer::factory()->count(4)->create();
        $this->assertEquals(4, Chofer::count());
    }

    public function test_se_pueden_crear_multiples_servicios_con_factory()
    {
        Servicio::factory()->count(10)->create();
        $this->assertEquals(10, Servicio::count());
    }

    /** ========== PRUEBAS DE RELACIONES ========== */
    
    public function test_cliente_tiene_muchos_servicios()
    {
        $cliente = Cliente::factory()->create();
        Servicio::factory()->count(3)->create(['cliente_id' => $cliente->id]);
        $this->assertEquals(3, $cliente->servicios->count());
    }

    public function test_vehiculo_tiene_muchos_servicios()
    {
        $vehiculo = Vehiculo::factory()->create();
        Servicio::factory()->count(2)->create(['vehiculo_id' => $vehiculo->id]);
        $this->assertEquals(2, $vehiculo->servicios->count());
    }

    public function test_chofer_tiene_muchos_servicios()
    {
        $chofer = Chofer::factory()->create();
        Servicio::factory()->count(4)->create(['chofer_id' => $chofer->id]);
        $this->assertEquals(4, $chofer->servicios->count());
    }
}