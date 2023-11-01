<?php

namespace App\Helpers;

/**
 * Class Socialite.
 */
class Socialite
{
    /**
     * List of the accepted third party provider types to log in with.
     *
     * @return array
     */
    public function getAcceptedProviders(): array
    {
        return [
            'facebook',
            'google',
            'twitter',
            'twitch',
            'steam',
            'battlenet',
        ];
    }
}
