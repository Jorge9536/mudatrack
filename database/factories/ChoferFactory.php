<?php

namespace Database\Factories;

use App\Models\Chofer;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChoferFactory extends Factory
{
    protected $model = Chofer::class;

    public function definition(): array
    {
        $nombres = ['Juan', 'Pedro', 'Carlos', 'Luis', 'Miguel', 'Roberto', 'José', 'David'];
        $apellidos = ['Mamani', 'Quispe', 'Flores', 'Lima', 'Rojas', 'Choque', 'Torres', 'García'];
        
        return [
            'nombre_completo' => $this->faker->randomElement($nombres) . ' ' . $this->faker->randomElement($apellidos),
            'telefono' => $this->faker->unique()->numerify('#######'),
            'licencia' => $this->faker->unique()->numerify('#####'),
            'disponible' => $this->faker->boolean(70),
            'observaciones' => $this->faker->optional()->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => now(),
        ];
    }
}