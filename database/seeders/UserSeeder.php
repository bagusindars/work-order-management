<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::count();

        if (!$users) {
            $roles = Role::get();

            User::insert([
                [
                    'name' => 'Operator account 1',
                    'email' => 'operator1@gmail.com',
                    'password' => Hash::make('12345678'),
                    'role_id' => $roles->where('key', 'operator')->first()->id,
                ],
                [
                    'name' => 'Operator account 2',
                    'email' => 'operator2@gmail.com',
                    'password' => Hash::make('12345678'),
                    'role_id' => $roles->where('key', 'operator')->first()->id,
                ],
                [
                    'name' => 'Production Manager',
                    'email' => 'pm@gmail.com',
                    'password' => Hash::make('12345678'),
                    'role_id' => $roles->where('key', 'production_manager')->first()->id,
                ]
            ]);
        }
    }
}
