<?php

namespace Database\Seeders;

use App\Models\Empresa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresas = [
            'Apple',
            'Microsoft',
            'Amazon',
            'Google',
            'Netflix',
            'Meta',
            'Tesla',
            'Samsung',
            'Mercado Livre',
            'Nubank',
            'PicPay',
            'Itaú',
            'OLX',
            'Bradesco',
            'Globo',
            'Spotify'
        ];
        foreach ($empresas as $empresa) {
            Empresa::create(['nome' => $empresa]);
        }
    }
}
