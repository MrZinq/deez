<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(PlatformsTableSeeder::class);
        $this->call(DigitalsTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(BackportMenuTableSeeder::class);
        $this->call(UserSeeder::class);
    }
}
