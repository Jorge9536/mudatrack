<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Chofer;
use App\Models\Ayudante;
use App\Models\Vehiculo;
use App\Models\Servicio;
use App\Models\Bien;
use App\Models\Deuda;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as FakerFactory;

class DatabaseSeeder extends Seeder
{
    protected $faker;

    public function run(): void
    {
        $this->faker = FakerFactory::create('es_ES');

        // 1. CREAR USUARIO ADMIN
        User::create([
            'name' => 'Admin MudaTrack',
            'email' => 'admin@mudatrack.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        // 2. CREAR CHOFERES (5)
        $choferes = Chofer::factory()->count(5)->create();

        // 3. CREAR AYUDANTES (8)
        $ayudantes = Ayudante::factory()->count(8)->create();

        // 4. CREAR VEHÍCULOS (6)
        $vehiculos = Vehiculo::factory()->count(6)->create();

        // 5. CREAR CLIENTES (25)
        $clientes = Cliente::factory()->count(25)->create();

        // 6. CREAR SERVICIOS (24)
        $estados = ['pendiente', 'confirmado', 'en_progreso', 'finalizado', 'cancelado', 'pendiente_pago', 'pagado'];
        
        for ($i = 0; $i < 24; $i++) {
            $cliente = $clientes->random();
            $chofer = $choferes->random();
            $vehiculo = $vehiculos->random();
            
            // Calcular costo
            $costoBase = $this->faker->randomElement([200, 250, 300, 350, 400, 450, 500, 600]);
            $ayudantesCosto = $this->faker->numberBetween(0, 3) * 80;
            $pisosCosto = max(0, $this->faker->numberBetween(1, 4) - 1) * 20;
            $callejonCosto = $this->faker->boolean(20) ? 30 : 0;
            $costoTotal = $costoBase + $ayudantesCosto + $pisosCosto + $callejonCosto;

            // Seleccionar estado con distribución realista
            $estado = $this->faker->randomElement($estados);

            $servicio = Servicio::create([
                'cliente_id' => $cliente->id,
                'vehiculo_id' => $vehiculo->id,
                'chofer_id' => $chofer->id,
                'origen' => $this->faker->randomElement([
                    'Av. 6 de Agosto, La Paz',
                    'Calle 12, El Alto',
                    'Av. Villazón, La Paz',
                    'Miraflores, La Paz',
                    'Zona Villa Adela, El Alto',
                    'Av. Busch, La Paz'
                ]),
                'destino' => $this->faker->randomElement([
                    'Calle 15, El Alto',
                    'Av. Costanera, El Alto',
                    'Sopocachi, La Paz',
                    'Zona Senkata, El Alto',
                    'Av. 16 de Julio, La Paz',
                    'Ciudad Satélite, El Alto'
                ]),
                'fecha_servicio' => $this->faker->dateTimeBetween('-60 days', 'now')->format('Y-m-d'),
                'hora_inicio' => $this->faker->optional(0.7)->dateTimeBetween('-60 days', 'now'),
                'hora_fin' => $this->faker->optional(0.5)->dateTimeBetween('-60 days', 'now'),
                'cantidad_ayudantes' => $this->faker->numberBetween(0, 3),
                'numero_pisos' => $this->faker->numberBetween(1, 4),
                'es_callejon' => $this->faker->boolean(20),
                'costo_total' => $costoTotal,
                'metodo_pago' => $estado === 'pagado' ? $this->faker->randomElement(['efectivo', 'qr']) : null,
                'estado' => $estado,
                'observaciones' => $this->faker->optional(0.3)->sentence(),
                'created_at' => $this->faker->dateTimeBetween('-60 days', 'now'),
                'updated_at' => now(),
            ]);

            // Crear bienes para el servicio (2-5 bienes)
            $numBienes = $this->faker->numberBetween(2, 5);
            for ($j = 0; $j < $numBienes; $j++) {
                Bien::create([
                    'servicio_id' => $servicio->id,
                    'nombre' => $this->faker->randomElement([
                        'Sofá 3 cuerpos', 'Refrigerador', 'Televisor 55"', 
                        'Camas', 'Cocina', 'Ropero', 'Mesas', 'Sillas', 
                        'Cajas de ropa', 'Electrodomésticos', 'Escritorio', 'Lavadora'
                    ]),
                    'cantidad' => $this->faker->numberBetween(1, 5),
                    'descripcion' => $this->faker->optional(0.3)->sentence(),
                    'created_at' => $servicio->created_at,
                    'updated_at' => now(),
                ]);
            }

            // Si el servicio está en pendiente_pago, crear deuda
            if ($estado === 'pendiente_pago') {
                Deuda::create([
                    'cliente_id' => $cliente->id,
                    'servicio_id' => $servicio->id,
                    'monto' => $costoTotal,
                    'fecha_vencimiento' => $this->faker->dateTimeBetween('now', '+30 days'),
                    'estado' => 'pendiente',
                    'observaciones' => 'Deuda pendiente de pago',
                    'created_at' => $servicio->created_at,
                    'updated_at' => now(),
                ]);
            }
        }

        // 7. ALGUNOS CLIENTES MOROSOS (3-4)
        $clientesMorosos = $clientes->random(4);
        foreach ($clientesMorosos as $cliente) {
            for ($i = 0; $i < 2; $i++) {
                $servicio = Servicio::create([
                    'cliente_id' => $cliente->id,
                    'vehiculo_id' => $vehiculos->random()->id,
                    'chofer_id' => $choferes->random()->id,
                    'origen' => $this->faker->randomElement(['Av. 6 de Agosto, La Paz', 'Calle 12, El Alto']),
                    'destino' => $this->faker->randomElement(['Calle 15, El Alto', 'Sopocachi, La Paz']),
                    'fecha_servicio' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
                    'hora_inicio' => $this->faker->dateTimeBetween('-30 days', 'now'),
                    'hora_fin' => null,
                    'cantidad_ayudantes' => $this->faker->numberBetween(1, 3),
                    'numero_pisos' => $this->faker->numberBetween(1, 3),
                    'es_callejon' => $this->faker->boolean(20),
                    'costo_total' => $this->faker->randomElement([200, 250, 300, 350]),
                    'metodo_pago' => null,
                    'estado' => 'pendiente_pago',
                    'observaciones' => 'Cliente moroso',
                    'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
                    'updated_at' => now(),
                ]);

                Deuda::create([
                    'cliente_id' => $cliente->id,
                    'servicio_id' => $servicio->id,
                    'monto' => $servicio->costo_total,
                    'fecha_vencimiento' => $this->faker->dateTimeBetween('-10 days', '+10 days'),
                    'estado' => 'pendiente',
                    'observaciones' => 'Cliente con deuda pendiente',
                    'created_at' => $servicio->created_at,
                    'updated_at' => now(),
                ]);
            }
            $cliente->update(['bloqueado' => true]);
        }

        $this->command->info('✅ Datos de prueba creados exitosamente!');
        $this->command->info('📊 Total de servicios: 24');
        $this->command->info('👥 Total de clientes: 25');
        $this->command->info('🚚 Total de choferes: 5');
        $this->command->info('🚗 Total de vehículos: 6');
        $this->command->info('🔧 Total de ayudantes: 8');
    }
}