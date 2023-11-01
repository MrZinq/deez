<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Listing;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SeoController extends Controller
{
    /**
     * Sitemap index.
     *
     * @return Response
     */
    public function sitemapIndex(): Response
    {
        return response()->view('seo.sitemap.index', [
            'listing'   => Listing::orderBy('updated_at', 'desc')->select('updated_at')->first(),
            'games'     => Game::orderBy('updated_at', 'desc')->select('updated_at')->first(),
        ])->header('Content-Type', 'text/xml');
    }

    /**
     * Sitemap for all listings.
     *
     * @return Response
     */
    public function sitemapListings(): Response
    {
        return response()->view('seo.sitemap.listings', [
            'listings' => Listing::all(),
        ])->header('Content-Type', 'text/xml');
    }

    /**
     * Sitemap for all games.
     *
     * @return Response
     */
    public function sitemapGames(): Response
    {
        return response()->view('seo.sitemap.games', [
            'games' => Game::all(),
        ])->header('Content-Type', 'text/xml');
    }

    /**
     * Fill the opensearch xml file with values.
     *
     * @return Response
     */
    public function openSearch(): Response
    {
        return response()->view('seo.xml.opensearch', [
            'url'           => url('/'),
            'name'          => config('settings.page_name'),
            'route_string'  => url('search').'/{searchTerms}',
            'description'   => config('settings.meta_description'),
        ])->header('Content-Type', 'text/xml');
    }

    /**
     * Fill the robots.txt file with values.
     *
     * @return View
     */
    public function robots(): View
    {
        return view('seo.robots', [
            'sitemap' => url('/sitemap'),
        ]);
    }
}
