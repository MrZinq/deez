<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Wiledia\Themes\Facades\Theme;

/**
 * Class ThemeController.
 */
class ThemeController extends Controller
{
    /**
     * Swap theme.
     *
     * @param string $theme
     * @return RedirectResponse
     */
    public function swap(string $theme): RedirectResponse
    {
        // Check if theme selector is enable or the user have access to the settings
        if (config('settings.theme_selector') || (auth()->check() && auth()->user()->can('edit_settings'))) {
            // get all themes
            $themes = Theme::all();

            // check if theme exist and change session setting
            foreach ($themes as $theme_check) {
                if ($theme_check['slug'] === $theme) {
                    // Check if theme is public or the user have access to the settings
                    if ($theme_check['public'] || (auth()->check() && auth()->user()->can('edit_settings'))) {
                        session()->put('theme', $theme);
                    }
                }
            }
        }

        // redirect back to last page
        return url()->current() === url()->previous() ? redirect()->route('index') : redirect()->back();
    }
}
