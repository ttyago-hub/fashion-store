<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@admin.com'], // evita duplicados
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'), // puedes cambiar la clave
                'role' => 'admin',
            ]
        );
    }
}
