<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            'Backend',
            'Frontend',
            'UX/UI',
            'Segurança da Informação',
            'DevOps',
            'Gerenciamento',
            'Redes',
            'Criptografia',
            'Hardware',
            'Kubernetes',
            'Infraestrutura',
            'Mobile'
        ];
        foreach($areas as $area){
            Area::create(['nome' => $area]);
        }
    }
}
