<?php

namespace Database\Factories;

use App\Models\Cargo;
use App\Models\Empresa;
use App\Models\Mentor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mentor>
 */
class MentorFactory extends Factory
{

    public static array $usados = [];

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
        $diff = array_diff($diff->toArray(), self::$usados);
        $userId = fake()->randomElement($diff);
        self::$usados[] = $userId;
        return [
            'cargo_id' => fake()->randomElement($cargos),
            'empresa_id' => fake()->randomElement($empresas),
            'curriculo' => fake()->text($maxNbChars = 100),
            'biografia' => fake()->paragraphs($nb = 3, $asText = true),
            'preco' => fake()->randomFloat($nbMaxDecimals = 2, $min = 15000, $max = 85000),
            'avaliacao' => fake()->numberBetween($min = 1, $max = 5),
            'user_id' => $userId,
            'minutos_por_chamada' => fake()->numberBetween($min = 10, $max = 90),
            'quantidade_chamadas' => fake()->numberBetween($min = 1, $max = 5),
        ];
    }
}
