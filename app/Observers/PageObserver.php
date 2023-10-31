<?php

namespace App\Observers;

use App\Models\Page;
use Illuminate\Support\Facades\Cache;
use Wiledia\Backport\Settings\Setting;

class PageObserver
{
    /**
     * Listen to the Page deleting event.
     *
     * @param Page $page
     * @return void
     */
    public function deleting(Page $page): void
    {
        // Check if terms of service or privacy policy page is set in settings
        if (config('settings.terms_service') || config('settings.privacy_policy')) {
            // Check if deleted page is saved as terms of service page. If yes, remove the page id from the settings.
            if ($page->id === config('settings.terms_service')) {
                $terms_service = Setting::where('key', 'terms_service')->first();
                $terms_service->value = 0;
                $terms_service->save();
            }
            // Check if deleted page is saved as privacy policy page. If yes, remove the page id from the settings.
            if ($page->id === config('settings.privacy_policy')) {
                $privacy_policy = Setting::where('key', 'privacy_policy')->first();
                $privacy_policy->value = 0;
                $privacy_policy->save();
            }
        }

        // Remove page cache
        Cache::forget('terms_service_page');
        Cache::forget('privacy_policy_page');
    }

    /**
     * Listen to the Page deleting event.
     *
     * @param Page $page
     * @return void
     */
    public function updated(Page $page): void
    {
        // Remove page cache
        Cache::forget('terms_service_page');
        Cache::forget('privacy_policy_page');
    }
}
