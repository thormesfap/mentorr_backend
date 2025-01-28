<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Habilidade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HabilidadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $backendArea = Area::firstWhere('nome', 'Backend');
        $habilidadesBackend = [
            'PHP',
            'Python',
            'JavaScript',
            'Ruby',
        ];
        $frontendArea = Area::firstWhere('nome', 'Frontend');
        $habilidadesFrontend = [
            'Javascript',
            'HTML',
            'CSS',
            'React',
            'Vue',
            'Angular',
        ];
        foreach ($habilidadesBackend as $habilidade) {
            $habil = new Habilidade();
            $habil->area()->associate($backendArea);
            $habil->nome = $habilidade;
            $habil->save();
        }

        foreach ($habilidadesFrontend as $habilidade) {
            $habil = new Habilidade();
            $habil->area()->associate($frontendArea);
            $habil->nome = $habilidade;
            $habil->save();
        }
    }
}
