<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'email' => 'admin@example.com', 'password' => 'admin123'],
            ['name' => 'patient', 'email' => 'patient@example.com', 'password' => 'patient123'],
            ['name' => 'personnel', 'email' => 'personnel@example.com', 'password' => 'personnel123'],
        ];

        foreach ($roles as $data) {
            $role = Role::create(['name' => $data['name']]);

            User::create([
                'name' => ucfirst($data['name']),
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => $role->id,
            ]);
        }
    }
}

