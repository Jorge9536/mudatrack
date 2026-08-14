<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        $nombres = ['Juan', 'María', 'Carlos', 'Ana', 'Luis', 'Laura', 'Pedro', 'Sofía', 'Jorge', 'Elena'];
        $apellidos = ['Pérez', 'Flores', 'Quispe', 'Mamani', 'Lima', 'García', 'Rojas', 'Choque', 'Mendoza', 'Torres'];
        
        return [
            'nombre_completo' => $this->faker->randomElement($nombres) . ' ' . $this->faker->randomElement($apellidos),
            'telefono' => $this->faker->unique()->numerify('#######'),
            'direccion' => $this->faker->randomElement([
                'Av. 6 de Agosto, La Paz',
                'Calle 12, El Alto',
                'Av. Villazón, La Paz',
                'Calle 15, El Alto',
                'Av. Busch, La Paz',
                'Zona Villa Adela, El Alto',
                'Miraflores, La Paz',
                'Zona Senkata, El Alto'
            ]),
            'latitud' => $this->faker->randomFloat(7, -16.5500, -16.4500),
            'longitud' => $this->faker->randomFloat(7, -68.2000, -68.1000),
            'bloqueado' => $this->faker->boolean(10),
            'observaciones' => $this->faker->optional()->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => now(),
        ];
    }
}