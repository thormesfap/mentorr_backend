<?php

namespace Database\Seeders;

use App\Models\Cargo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CargoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cargos = [
            'Engenheiro de Software',
            'Desenvolvedor Full Stack',
            'Desenvolvedor Backend',
            'Desenvolvedor Frontend',
            'Analista de Dados',
            'Gerente de Projetos',
            'Designer UX/UI',
            'Especialista em Segurança da Informação',
            'Gerente de Tecnologia',
            'Arquiteto de Software',
            'Especialista em Redes',
            'Desenvolvedor Mobile',
            'Especialista em IA'
        ];
        foreach ($cargos as $cargo) {
            Cargo::create(['nome' => $cargo]);
        }
    }
}
