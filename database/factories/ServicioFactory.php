<?php

namespace Database\Factories;

use App\Models\Servicio;
use App\Models\Cliente;
use App\Models\Chofer;
use App\Models\Vehiculo;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServicioFactory extends Factory
{
    protected $model = Servicio::class;

    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'chofer_id' => Chofer::factory(),
            'vehiculo_id' => Vehiculo::factory(),
            'origen' => $this->faker->randomElement([
                'Av. 6 de Agosto, La Paz',
                'Calle 12, El Alto',
                'Av. Villazón, La Paz',
                'Miraflores, La Paz'
            ]),
            'destino' => $this->faker->randomElement([
                'Calle 15, El Alto',
                'Av. Costanera, El Alto',
                'Sopocachi, La Paz',
                'Zona Senkata, El Alto'
            ]),
            'fecha_servicio' => $this->faker->dateTimeBetween('-60 days', 'now'),
            'hora_inicio' => $this->faker->optional(0.7)->dateTimeBetween('-60 days', 'now'),
            'hora_fin' => $this->faker->optional(0.5)->dateTimeBetween('-60 days', 'now'),
            'cantidad_ayudantes' => $this->faker->numberBetween(0, 3),
            'numero_pisos' => $this->faker->numberBetween(1, 4),
            'es_callejon' => $this->faker->boolean(20),
            'costo_total' => $this->faker->randomFloat(2, 200, 600),
            'metodo_pago' => null,
            'estado' => $this->faker->randomElement(['pendiente', 'confirmado', 'en_progreso', 'finalizado', 'cancelado', 'pendiente_pago', 'pagado']),
            'observaciones' => $this->faker->optional(0.3)->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-60 days', 'now'),
            'updated_at' => now(),
        ];
    }
}