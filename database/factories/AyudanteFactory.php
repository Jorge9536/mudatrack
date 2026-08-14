<?php

namespace Database\Factories;

use App\Models\Ayudante;
use Illuminate\Database\Eloquent\Factories\Factory;

class AyudanteFactory extends Factory
{
    protected $model = Ayudante::class;

    public function definition(): array
    {
        $nombres = ['Luis', 'Carlos', 'Roberto', 'Andrés', 'Fernando', 'Javier', 'Ricardo', 'Daniel'];
        $apellidos = ['Mamani', 'Quispe', 'Flores', 'Lima', 'Rojas', 'Choque', 'Torres', 'García'];
        
        return [
            'nombre_completo' => $this->faker->randomElement($nombres) . ' ' . $this->faker->randomElement($apellidos),
            'telefono' => $this->faker->unique()->numerify('#######'),
            'disponible' => $this->faker->boolean(80),
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => now(),
        ];
    }
}