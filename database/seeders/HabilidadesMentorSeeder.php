<?php

namespace Database\Seeders;

use App\Models\Habilidade;
use App\Models\Mentor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HabilidadesMentorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mentores = Mentor::all();
        $habilidades = Habilidade::all()->pluck('id')->toArray();
        foreach ($mentores as $mentor) {
            $random = fake()->randomElements($habilidades, 3);
            $mentor->habilidades()->sync($random);
            $mentor->save();
        }
    }
}
