<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Wiledia\Backport\Auth\Database\Permission;
use Wiledia\Backport\Auth\Database\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run(): void
    {
        $roles = [
            [
                'data' => [
                    'slug' => 'admin',
                    'name' => 'Admin',
                ],
                'permissions' => [
                    '*',
                ],
            ],
            [
                'data' => [
                    'slug' => 'moderator',
                    'name' => 'Moderator',
                ],
                'permissions' => [
                    'access_backend',
                    'edit_games',
                    'edit_listings',
                    'edit_platforms',
                    'edit_comments',
                    'edit_pages',
                    'edit_articles',
                ],
            ],
        ];

        foreach ($roles as $role) {
            $roleEntity = Role::firstOrCreate(
                ['slug' => $role['data']['slug']],
                $role['data']
            );

            // Attach digital distributors to the platforms
            foreach ($role['permissions'] ?? [] as $permission) {
                // Attach all permissions to the role
                if ($permission === '*') {
                    $permissionsEntity = Permission::all();

                    foreach ($permissionsEntity as $permissionEntity) {
                        $roleEntity->permissions()->syncWithoutDetaching($permissionEntity);
                    }

                    continue;
                }

                // Attach selected permissions to the role
                $permissionEntity = Permission::where('slug', $permission)->first();

                if ($permissionEntity) {
                    $roleEntity->permissions()->syncWithoutDetaching($permissionEntity);
                }
            }
        }
    }
}
