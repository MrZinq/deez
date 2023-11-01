<?php

namespace Database\Seeders;

use App\Models\Digital;
use App\Models\Platform;
use Illuminate\Database\Seeder;

class DigitalsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run(): void
    {
        $digitals = [
            [
                'name' => 'Steam',
                'platforms' => [
                    'pc',
                ],
            ],
            [
                'name' => 'Origin',
                'platforms' => [
                    'pc',
                ],
            ],
            [
                'name' => 'Battle.net',
                'platforms' => [
                    'pc',
                ],
            ],
            [
                'name' => 'Ubisoft Connect',
                'platforms' => [
                    'pc',
                ],
            ],
            [
                'name' => 'GOG',
                'platforms' => [
                    'pc',
                ],
            ],
            [
                'name' => 'Epic Games',
                'platforms' => [
                    'pc',
                ],
            ],
            [
                'name' => 'Microsoft Store',
                'platforms' => [
                    'pc',
                ],
            ],
            [
                'name' => 'Rockstar Games Launcher',
                'platforms' => [
                    'pc',
                ],
            ],
            [
                'name' => 'PlayStation Network',
                'platforms' => [
                    'ps3',
                    'ps4',
                    'ps5',
                    'vita',
                ],
            ],
            [
                'name' => 'Xbox Live',
                'platforms' => [
                    'xbox360',
                    'xboxone',
                    'xboxseries',
                ],
            ],
            [
                'name' => 'Nintendo eShop',
                'platforms' => [
                    '3ds',
                    'wii-u',
                    'switch',
                ],
            ],
        ];

        foreach ($digitals as $digital) {
            $digitalEntity = Digital::firstOrCreate(
                ['name' => $digital['name']]
            );

            // Attach digital distributors to the platforms
            foreach ($digital['platforms'] ?? [] as $platform) {
                $platformEntity = Platform::where('acronym', $platform)->first();

                $platformEntity?->digitals()->syncWithoutDetaching($digitalEntity);
            }
        }
    }
}
