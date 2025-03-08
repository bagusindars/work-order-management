<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::count();

        if (!$roles) {
            Role::Insert([
                ['label' => 'Production Manager', 'key' => 'production_manager'],
                ['label' => 'Operator', 'key' => 'operator'],
            ]);
        }
    }
}
