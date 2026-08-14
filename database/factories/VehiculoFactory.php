<?php

namespace Database\Factories;

use App\Models\Vehiculo;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehiculoFactory extends Factory
{
    protected $model = Vehiculo::class;

    public function definition(): array
    {
        $marcas = ['Toyota', 'Mercedes', 'Ford', 'Chevrolet', 'Mitsubishi', 'Hino', 'Isuzu', 'Volkswagen'];
        $modelos = ['Hilux', 'Sprinter', 'F-150', 'NPR', 'Canter', 'Transit', 'D-Max', 'Amarok'];
        $tipos = ['3ton', '6ton', 'chata'];
        
        return [
            'placa' => $this->faker->unique()->regexify('[A-Z0-9]{3}-[A-Z0-9]{3}'),
            'marca' => $this->faker->randomElement($marcas),
            'modelo' => $this->faker->randomElement($modelos),
            'tipo' => $this->faker->randomElement($tipos),
            'capacidad_kg' => $this->faker->randomElement([1500, 3000, 6000, 8000]),
            'disponible' => $this->faker->boolean(70),
            'observaciones' => $this->faker->optional()->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => now(),
        ];
    }
}