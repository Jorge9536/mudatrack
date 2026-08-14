<?php

namespace Database\Factories;

use App\Models\Bien;
use App\Models\Servicio;
use Illuminate\Database\Eloquent\Factories\Factory;

class BienFactory extends Factory
{
    protected $model = Bien::class;

    public function definition(): array
    {
        $bienes = ['Sofá 3 cuerpos', 'Refrigerador', 'Televisor 55"', 'Camas', 'Cocina', 'Ropero', 'Mesas', 'Sillas', 'Cajas de ropa', 'Electrodomésticos'];
        
        return [
            'servicio_id' => Servicio::factory(),
            'nombre' => $this->faker->randomElement($bienes),
            'cantidad' => $this->faker->numberBetween(1, 5),
            'descripcion' => $this->faker->optional()->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'updated_at' => now(),
        ];
    }
}