<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@dharma.com'],
            ['name' => 'Administrator', 'password' => Hash::make('admin123')]
        );
        $admin->assignRole('admin');

        $user = User::firstOrCreate(
            ['email' => 'panitia@dharma.com'],
            ['name' => 'Panitia', 'password' => Hash::make('panitia123')]
        );
        $user->assignRole('user');
    }
}
