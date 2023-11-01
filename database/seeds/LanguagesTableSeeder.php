<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run(): void
    {
        $languages = [
            [
                'name' => 'English',
                'app_name' => 'english',
                'abbr' => 'en',
                'script' => 'Latn',
                'native' => 'English',
                'active' => 1,
                'default' => 1,
            ],
            [
                'name' => 'German',
                'app_name' => 'german',
                'abbr' => 'de',
                'script' => 'Latn',
                'native' => 'Deutsch',
                'active' => 1,
                'default' => 0,
            ],
        ];

        foreach ($languages as $language) {
            Language::firstOrCreate(
                ['app_name' => $language['app_name']],
                $language
            );
        }
    }
}
