<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash; // <-- JANGAN SAMPAI KETINGGALAN INI

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Mrupy Agung',
                'email' => 'mrupy@example.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti@example.com',
                'password' => Hash::make('password123'),
                'role' => 'employee',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'password' => Hash::make('password123'),
                'role' => 'employee',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@example.com',
                'password' => Hash::make('password123'),
                'role' => 'direktur',
            ],
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad@example.com',
                'password' => Hash::make('password123'),
                'role' => 'employee',
            ],
            [
                'name' => 'Rina Melati',
                'email' => 'rina@example.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ],
            [
                'name' => 'Joko Widodo',
                'email' => 'joko@example.com',
                'password' => Hash::make('password123'),
                'role' => 'employee',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}