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
        'name' => 'Admin',
        'email' => 'mlouki.server@gmail.com',
        'password' => Hash::make('adminpassword'),
        'role' => 'admin',
        'phone_number'=>'0611223344'
    ]);

    }


}
