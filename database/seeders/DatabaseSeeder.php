<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Role;
use App\Models\Mentor;

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
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@mentorr.com',
            'password' => bcrypt('12345678'),
        ]);
        $role_admin = Role::where('name', 'role_admin')->get()->first();
        $admin->roles()->attach($role_admin);
        $admin->save();

        User::factory(20)->create();
        Mentor::factory(10)->create();

        $this->call([
            HabilidadesMentorSeeder::class,
        ]);
    }
}
