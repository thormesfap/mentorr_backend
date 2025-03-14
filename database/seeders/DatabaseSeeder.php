<?php

namespace Database\Seeders;

use App\Models\Mentor;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            AreaSeeder::class,
            CargoSeeder::class,
            EmpresaSeeder::class,
            HabilidadeSeeder::class,
        ]);
        User::factory(20)->create();
        Mentor::factory(10)->create();

        $this->call([
            HabilidadesMentorSeeder::class,
        ]);
    }
}
