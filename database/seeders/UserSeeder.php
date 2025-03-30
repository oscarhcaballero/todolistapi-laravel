<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'User One',
                'email' => 'user1@example.com',
                'password' => Hash::make('password1'),
            ],
            [
                'name' => 'User Two',
                'email' => 'user2@example.com',
                'password' => Hash::make('password2'),
            ],
            [
                'name' => 'User Three',
                'email' => 'user3@example.com',
                'password' => Hash::make('password3'),
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);

            // Generar un token para cada usuario
            $token = $user->createToken('API Token')->plainTextToken;

            // Mostrar el token en la consola (opcional)
            echo "Generated token for {$user->email}: {$token}\n";
        }
    }
}
