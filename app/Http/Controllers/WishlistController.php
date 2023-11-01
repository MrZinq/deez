<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Wishlist;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Prologue\Alerts\Facades\Alert;

class WishlistController extends Controller
{
    /**
     * Index wishlist.
     *
     * @return View
     */
    public function index(): View
    {
        // Get all wishlist entries from this user
        $wishlists = Wishlist::where('user_id', auth()->id())
                             ->with('game', 'listings', 'listings.game', 'listings.game.platform', 'listings.user')
                             ->orderBy('created_at', 'desc')
                             ->paginate('10');

        // SEO Page Title
        SEOMeta::setTitle(trans('wishlist.wishlist').' - '.config('settings.page_name').' » '.config('settings.sub_title'));

        return view('frontend.wishlist.index', ['wishlists' => $wishlists]);
    }

    /**
     * Add item to wishlist.
     *
     * @param Request $request
     * @param string $slug
     * @return RedirectResponse
     */
    public function add(Request $request, string $slug): RedirectResponse
    {
        // Get game id from slug string
        $game_id = ltrim(strrchr($slug, '-'), '-');
        $game = Game::find($game_id);

        // Check if game exists
        if (is_null($game)) {
            abort('404');
        }

        // Check if game is already in the wishlist
        $wishlist_check = Wishlist::where('game_id', $game->id)->where('user_id', auth()->id())->first();

        if (isset($wishlist_check)) {
            // show a error message
            Alert::error('<i class="fas fa-times m-r-5"></i> '.trans('wishlist.alert.exists', ['game_name' => str_replace("'", '', $game->name)]))->flash();

            return redirect()->back();
        }

        // Get all input values
        $input = $request->all();

        // Create new wishlist
        $wishlist = new Wishlist;
        // Set game id
        $wishlist->game_id = $game->id;
        // Set user id
        $wishlist->user_id = auth()->id();

        // Check if user want to get a notification
        if ($request->has('wishlist-notification')) {
            $wishlist->notification = true;
            // Max price for the notification
            $max_price = filter_var($input['wishlist_price'], FILTER_SANITIZE_NUMBER_INT);
            if ($max_price > 0) {
                $wishlist->max_price = $max_price;
            }
        }

        // Save wishlist
        $wishlist->save();

        // show a success message
        Alert::success('<i class="fas fa-heart m-r-5"></i>'.trans('wishlist.alert.added', ['game_name' => str_replace("'", '', $game->name)]))->flash();

        return redirect()->back();
    }

    /**
     * Add item to wishlist.
     *
     * @param Request $request
     * @param string $slug
     * @return RedirectResponse
     */
    public function update(Request $request, string $slug): RedirectResponse
    {
        // Get game id from slug string
        $game_id = ltrim(strrchr($slug, '-'), '-');
        $game = Game::find($game_id);

        // Check if game exists
        if (is_null($game)) {
            abort('404');
        }

        // Check if item is in wishlist
        $wishlist = Wishlist::where('game_id', $game->id)->where('user_id', auth()->id())->first();

        if (! isset($wishlist)) {
            abort('404');
        }

        // Get all input values
        $input = $request->all();

        // Set game id
        $wishlist->game_id = $game->id;
        // Set user id
        $wishlist->user_id = auth()->id();

        // Check if user want to get a notification
        if ($request->has('wishlist-notification')) {
            $wishlist->notification = true;
            // Max price for the notification
            $max_price = filter_var($input['wishlist_price'], FILTER_SANITIZE_NUMBER_INT);
            if ($max_price > 0) {
                $wishlist->max_price = $max_price;
            } else {
                $wishlist->max_price = null;
            }
        } else {
            $wishlist->notification = false;
            $wishlist->max_price = null;
        }

        // Save wishlist
        $wishlist->save();

        // show a success message
        Alert::success('<i class="fas fa-heart m-r-5"></i> '.trans('wishlist.alert.saved', ['game_name' => str_replace("'", '', $game->name)]))->flash();

        return redirect()->back();
    }

    /**
     * Remove item from wishlist.
     *
     * @param Request $request
     * @param string $slug
     * @return RedirectResponse
     */
    public function delete(Request $request, string $slug): RedirectResponse
    {
        // Get game id from slug string
        $game_id = ltrim(strrchr($slug, '-'), '-');
        $game = Game::find($game_id);

        // Check if game exists
        if (is_null($game)) {
            abort('404');
        }

        // Check if game is already in the wishlist
        $wishlist = Wishlist::where('game_id', $game->id)->where('user_id', auth()->id())->first();

        // Check if wishlist entry exists, otherwise abort with a 404 error
        if (isset($wishlist)) {
            $wishlist->delete();
        } else {
            abort('404');
        }

        // show a success message
        Alert::error('<i class="far fa-heart"></i> '.trans('wishlist.alert.removed', ['game_name' => str_replace("'", '', $game->name)]))->flash();

        return redirect()->back();
    }
}
