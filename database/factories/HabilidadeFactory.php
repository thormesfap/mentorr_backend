<?php

namespace Database\Factories;

use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Habilidade>
 */
class HabilidadeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $area = Area::all()->pluck('id');
        return [
            'nome' => fake()->name(),
            'area_id' => fake()->randomElement($area)
        ];
    }
}
