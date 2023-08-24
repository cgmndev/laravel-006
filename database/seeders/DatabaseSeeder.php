<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Rol::create([
            'id' => Rol::ADMIN,
            'nombre' => 'Administrador',
            'descripcion' => 'Usuario Administrador',
        ]);

        Rol::create([
            'id' => Rol::PROFESOR,
            'nombre' => 'Profesor',
            'descripcion' => 'Usuario Profesor',
        ]);

        Rol::create([
            'id' => Rol::ESTUDIANTE,
            'nombre' => 'Estudiante',
            'descripcion' => 'Usuario Estudiante',
        ]);

        User::factory()->create([
            'rol_id' => Rol::ADMIN,
            'name' => 'Admin',
            'email' => 'claudio.mardones@lazos.cl',
            'password' => Hash::make('12345678'),
        ]);
    }
}
