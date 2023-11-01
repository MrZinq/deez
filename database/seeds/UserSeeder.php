<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Wiledia\Backport\Auth\Database\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Skip seed if admin user already exists.
        if (User::whereName('admin')->exists()) {
            return;
        }

        // Create admin user
        $user = User::create([
            'name'     => 'admin',
            'email'    => 'admin@admin.com',
            'password' => bcrypt('adminadmin'),
            'status'   => true,
        ]);

        // Get admin role
        $adminRole = Role::whereSlug('admin')->first();

        // Assign admin role
        if ($adminRole) {
            $user->roles()->attach($adminRole);
        }
    }
}
