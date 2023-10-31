<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Wiledia\Backport\Auth\Database\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run(): void
    {
        $permissions = [
            [
                'slug' => 'access_backend',
                'name' => 'Access backend',
                'http_path' => "/\r\n/update/*",
                'http_method' => 'GET',
            ],
            [
                'slug' => 'edit_games',
                'name' => 'Edit games',
                'http_path' => "/games*\r\n/genres*",
                'http_method' => null,
            ],
            [
                'slug' => 'edit_listings',
                'name' => 'Edit Listings',
                'http_path' => '/listings*',
                'http_method' => null,
            ],
            [
                'slug' => 'edit_platforms',
                'name' => 'Edit Platforms',
                'http_path' => "/platforms*\r\n/digital*",
                'http_method' => null,
            ],
            [
                'slug' => 'edit_users',
                'name' => 'Edit Users',
                'http_path' => "/users*\r\n/roles*\r\n/permissions*",
                'http_method' => null,
            ],
            [
                'slug' => 'edit_ratings',
                'name' => 'Edit Ratings',
                'http_path' => '/ratings*',
                'http_method' => null,
            ],
            [
                'slug' => 'edit_settings',
                'name' => 'Edit Settings',
                'http_path' => '/settings*',
                'http_method' => null,
            ],
            [
                'slug' => 'edit_translations',
                'name' => 'Edit Translations',
                'http_path' => "/language*\r\n/translation*",
                'http_method' => null,
            ],
            [
                'slug' => 'edit_offers',
                'name' => 'Edit Offers',
                'http_path' => "/offers*\r\n/reports*",
                'http_method' => null,
            ],
            [
                'slug' => 'edit_pages',
                'name' => 'Edit Pages',
                'http_path' => "/pages*\r\n/menu-item*",
                'http_method' => null,
            ],
            [
                'slug' => 'edit_comments',
                'name' => 'Edit Comments',
                'http_path' => '/comments*',
                'http_method' => null,
            ],
            [
                'slug' => 'edit_payments',
                'name' => 'Edit Payments',
                'http_path' => "/payments*\r\n/transactions*\r\n/withdrawals*",
                'http_method' => null,
            ],
            [
                'slug' => 'edit_articles',
                'name' => 'Edit Articles',
                'http_path' => "/articles*\r\n/categories*\r\n/tags*",
                'http_method' => null,
            ],
            [
                'slug' => 'access_logs',
                'name' => 'Access Logs',
                'http_path' => '/logs*',
                'http_method' => null,
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
