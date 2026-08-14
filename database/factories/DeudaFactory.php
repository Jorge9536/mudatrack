<?php

namespace Database\Factories;

use App\Models\Deuda;
use App\Models\Cliente;
use App\Models\Servicio;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeudaFactory extends Factory
{
    protected $model = Deuda::class;

    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'servicio_id' => Servicio::factory(),
            'monto' => $this->faker->randomFloat(2, 100, 500),
            'fecha_vencimiento' => $this->faker->dateTimeBetween('now', '+30 days'),
            'estado' => $this->faker->randomElement(['pendiente', 'pagado', 'vencido']),
            'observaciones' => $this->faker->optional()->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-60 days', 'now'),
            'updated_at' => now(),
        ];
    }
}