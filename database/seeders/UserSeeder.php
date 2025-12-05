<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'fname' => 'Admin',
            'lname' => 'User',
            'email' => 'thrift@admin.com',
            'password' => Hash::make('thriftadmin2025'),
            'role' => '2',
            'email_verified_at' => now(),
        ]);

        User::create([
            'fname' => 'Upcycler',
            'lname' => 'User',
            'email' => 'upcycler@example.com',
            'password' => Hash::make('password123'),
            'role' => '1',
            'email_verified_at' => now(),
        ]);

        // Regular users
        $regularUsers = [
            ['fname' => 'Clint', 'lname' => 'Alonzo', 'email' => 'aicsalonzo@gmail.com'],
            ['fname' => 'Jasmine', 'lname' => 'Lopez', 'email' => 'jaz.lopez@example.com'],
            ['fname' => 'Mark', 'lname' => 'Santos', 'email' => 'mark.santos@example.com'],
            ['fname' => 'Ella', 'lname' => 'Cruz', 'email' => 'ella.cruz@example.com'],
            ['fname' => 'Kyle', 'lname' => 'Reyes', 'email' => 'kyle.reyes@example.com'],
        ];

        foreach ($regularUsers as $user) {
            User::create([
                'fname' => $user['fname'],
                'lname' => $user['lname'],
                'email' => $user['email'],
                'password' => Hash::make('password123'),
                'role' => '0',
                'email_verified_at' => now(),
            ]);
        }
    }
}
