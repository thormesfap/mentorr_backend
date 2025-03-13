<?php

namespace Database\Factories;

use App\Models\Cargo;
use App\Models\Empresa;
use Faker\Factory;

class MentorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $diff = User::all()->pluck('id')->diffKeys(Mentor::all()->pluck('user_id'));
        $cargos = Cargo::all()->pluck('id');
        $empresas = Empresa::all()->pluck('id');
        return [
            'cargo_id' => fake()->randomElement($cargos),
            'empresa_id' => fake()->randomElement($empresas),
            'curriculo' => fake()->text($maxNbChars = 100),
            'biografia' => fake()->paragraphs($nb = 3, $asText = true),
            'preco' => fake()->randomFloat($nbMaxDecimals = 2, $min = 10, $max = 50),
            'avaliacao' => fake()->numberBetween($min = 1, $max = 5),
            'user_id' => fake()->randomElement($diff),
        ];
    }
}
