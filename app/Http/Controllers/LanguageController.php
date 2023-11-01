<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

/**
 * Class LanguageController.
 */
class LanguageController extends Controller
{
    /**
     * Changes language.
     *
     * @param string $lang
     * @return RedirectResponse
     */
    public function swap(string $lang): RedirectResponse
    {
        session()->put('locale', $lang);

        return redirect()->back();
    }
}
