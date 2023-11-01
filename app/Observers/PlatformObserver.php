<?php

namespace App\Observers;

use App\Models\Game;
use App\Models\Platform;

class PlatformObserver
{
    /**
     * Listen to the Platform deleting event.
     *
     * @param Platform $platform
     * @return void
     */
    public function deleting(Platform $platform): void
    {

        // Get all games
        $games = Game::where('platform_id', $platform->id)->get();

        foreach ($games as $game) {
            // remove game
            $game->delete();
        }
    }
}
