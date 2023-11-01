<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;

class PlatformsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run(): void
    {
        $platforms = [
            [
                'name' => 'PC',
                'color' => '#000000',
                'acronym' => 'pc',
                'cover_position' => 'left',
            ],
            [
                'name' => 'PlayStation 5',
                'description' => 'Sony',
                'color' => '#ffffff',
                'acronym' => 'ps5',
                'cover_position' => 'left',
                'cover_is_light' => true,
            ],
            [
                'name' => 'Xbox Series',
                'description' => 'Microsoft',
                'color' => '#71ae09',
                'acronym' => 'xboxseries',
                'cover_position' => 'left',
            ],
            [
                'name' => 'PlayStation 4',
                'description' => 'Sony',
                'color' => '#003791',
                'acronym' => 'ps4',
                'cover_position' => 'left',
            ],
            [
                'name' => 'Xbox One',
                'description' => 'Microsoft',
                'color' => '#107c10',
                'acronym' => 'xboxone',
                'cover_position' => 'center',
            ],
            [
                'name' => 'Nintendo Switch',
                'description' => 'Nintendo',
                'color' => '#e60012',
                'acronym' => 'switch',
                'cover_position' => 'left',
            ],
            [
                'name' => 'Wii U',
                'description' => 'Nintendo',
                'color' => '#009ac7',
                'acronym' => 'wii-u',
                'cover_position' => 'center',
            ],
            [
                'name' => 'PlayStation 3',
                'description' => 'Sony',
                'color' => '#326db3',
                'acronym' => 'ps3',
                'cover_position' => 'left',
            ],
            [
                'name' => 'Xbox 360',
                'description' => 'Microsoft',
                'color' => '#a4c955',
                'acronym' => 'xbox360',
                'cover_position' => 'left',
            ],
            [
                'name' => 'Nintendo 3DS',
                'description' => 'Nintendo',
                'color' => '#c90f17',
                'acronym' => '3ds',
                'cover_position' => 'left',
            ],
            [
                'name' => 'PlayStation Vita',
                'description' => 'Sony',
                'color' => '#1654bd',
                'acronym' => 'vita',
                'cover_position' => 'left',
            ],
            [
                'name' => 'Nintendo DS',
                'description' => 'Nintendo',
                'color' => '#929497',
                'acronym' => 'ds',
                'cover_position' => 'left',
            ],
            [
                'name' => 'PlayStation 2',
                'description' => 'Sony',
                'color' => '#140c7a',
                'acronym' => 'ps2',
                'cover_position' => 'left',
            ],
            [
                'name' => 'Xbox',
                'description' => 'Microsoft',
                'color' => '#93c83e',
                'acronym' => 'xbox',
                'cover_position' => 'left',
            ],
            [
                'name' => 'PlayStation',
                'description' => 'Sony',
                'color' => '#4081bc',
                'acronym' => 'ps',
                'cover_position' => 'left',
            ],
            [
                'name' => 'Wii',
                'description' => 'Nintendo',
                'color' => '#32ccfe',
                'acronym' => 'wii',
                'cover_position' => 'right',
            ],
            [
                'name' => 'Gamecube',
                'description' => 'Nintendo',
                'color' => '#663399',
                'acronym' => 'gamecube',
                'cover_position' => 'center',
            ],
            [
                'name' => 'Nintendo 64',
                'description' => 'Nintendo',
                'color' => '#fdbf2d',
                'acronym' => 'n64',
                'cover_position' => 'left',
            ],
            [
                'name' => 'Game Boy Advance',
                'description' => 'Nintendo',
                'color' => '#1f00cc',
                'acronym' => 'gba',
                'cover_position' => 'left',
            ],
            [
                'name' => 'PlayStation Portable',
                'description' => 'Sony',
                'color' => '#8e92af',
                'acronym' => 'psp',
                'cover_position' => 'left',
            ],
            [
                'name' => 'Dreamcast',
                'description' => 'Sega',
                'color' => '#4365a2',
                'acronym' => 'dreamcast',
                'cover_position' => 'left',
            ],
        ];

        foreach ($platforms as $platform) {
            Platform::firstOrCreate(
                ['acronym' => $platform['acronym']],
                $platform
            );
        }
    }
}
