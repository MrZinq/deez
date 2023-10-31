<?php

namespace App\Observers;

use App\Models\Language;
use Illuminate\Support\Facades\Cache;

class LanguageObserver
{
    /**
     * Listen to the Language created event.
     *
     * @param  Language  $language
     * @return void
     */
    public function created(Language $language): void
    {
        Cache::forget('languages');
    }

    /**
     * Listen to the Language updating event.
     *
     * @param  Language  $language
     * @return void
     */
    public function updated(Language $language): void
    {
        Cache::forget('languages');
    }

    /**
     * Listen to the Language deleted event.
     *
     * @param  Language  $language
     * @return void
     */
    public function deleted(Language $language): void
    {
        Cache::forget('languages');
    }
}
