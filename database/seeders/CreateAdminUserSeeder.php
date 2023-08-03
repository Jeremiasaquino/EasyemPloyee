<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'nombre' => 'Administrador Principal',
            'email' => 'adminprincipal@gmail.com',
            'role' => 'Administrador',
            'password' => bcrypt('admin2040'),
        ]);
    }
}
