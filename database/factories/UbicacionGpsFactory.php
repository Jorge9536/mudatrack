<?php

namespace Database\Factories;

use App\Models\UbicacionGps;
use App\Models\Servicio;
use Illuminate\Database\Eloquent\Factories\Factory;

class UbicacionGpsFactory extends Factory
{
    protected $model = UbicacionGps::class;

    public function definition(): array
    {
        return [
            'servicio_id' => Servicio::factory(),
            'latitud' => $this->faker->randomFloat(7, -16.5500, -16.4500),
            'longitud' => $this->faker->randomFloat(7, -68.2000, -68.1000),
            'velocidad' => $this->faker->optional(0.5)->randomFloat(2, 0, 60),
            'fecha_hora' => $this->faker->dateTimeBetween('-60 days', 'now'),
            'created_at' => $this->faker->dateTimeBetween('-60 days', 'now'),
            'updated_at' => now(),
        ];
    }
}