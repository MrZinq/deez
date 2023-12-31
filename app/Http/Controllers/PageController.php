<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Game;
use App\Models\Listing;
use App\Models\Page;
use App\Models\Platform;
use Artesaos\SEOTools\Facades\SEOTools as SEO;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Prologue\Alerts\Facades\Alert;

class PageController extends Controller
{
    /**
     * Startpage.
     *
     * @return View
     */
    public function startpage(): View
    {
        // Page title
        SEO::setTitle(trans('general.title.welcome', ['page_name' => config('settings.page_name'), 'sub_title' => config('settings.sub_title')]));

        // Listings query
        $listings = Cache::rememberForever('last_24_listings', function () {
            return Listing::with('game', 'game.giantbomb', 'game.platform', 'user', 'user.location')
                          ->where('status', '=', null)
                          ->orderby('created_at', 'desc')
                          ->whereHas('user', function ($query) {
                              $query->where('status', 1);
                          })
                          ->orWhere('status', '=', '0')
                          ->whereHas('user', function ($query) {
                              $query->where('status', 1);
                          })
                          ->limit(24)
                          ->get();
        });

        // Games query
        $popular_games = Cache::rememberForever('popular_games', function () {
            return Game::query()
                       ->with('platform', 'giantbomb', 'listingsCount', 'wishlistCount', 'metacritic')
                       ->withCount('heartbeat')
                       ->orderBy('heartbeat_count', 'desc')
                       ->limit('12')
                       ->get();
        });

        // Platforms query
        $platforms = Cache::rememberForever('popular_platforms', function () {
            return Platform::query()
                           ->withCount('games')
                           ->orderBy('games_count', 'desc')
                           ->limit('6')
                           ->get();
        });

        return view('frontend.pages.startpage', ['listings' => $listings, 'popular_games' => $popular_games, 'platforms' => $platforms]);
    }

    /**
     * Show page to user.
     *
     * @param string $slug
     * @return View
     */
    public function index(string $slug): View
    {
        $page = Page::findBySlug($slug);

        if (! $page) {
            abort(404);
        }

        $this->data['title'] = $page->title;
        $this->data['page'] = $page;

        // Page title
        SEO::setTitle($page->extras['meta_title'] ?? $page->title.' - '.config('settings.page_name').' » '.config('settings.sub_title'));

        // Page description
        SEO::setDescription($page->extras['meta_description'] ?? config('seotools.meta.defaults.description'));

        return view('frontend.pages.'.$page->template, $this->data);
    }

    /**
     * Sent contact form.
     *
     * @param Request $request
     * @return Redirector|RedirectResponse
     * @throws ValidationException
     */
    public function contact(Request $request): Redirector|RedirectResponse
    {
        $this->validate($request, [
            'name'      => 'required',
            'email'     => 'required|email',
            'message'   => 'required',
        ]);

        $data = [
            'email'         => $request->get('email'),
            'subject'       => '['.config('settings.page_name').'] New Message from '.$request->get('name'),
            'bodyMessage'   => $request->get('message'),
            'name'          => $request->get('name'),
        ];

        Mail::send('frontend.emails.contact', $data, function ($message) use ($data) {
            $message->from($data['email']);
            $message->to(config('settings.contact_email'));
            $message->subject($data['subject']);
        });

        // show a success message
        Alert::success('<i class="fa fa-send-o m-r-5"></i> '.trans('general.contact.successfully_sent'))->flash();

        // Page title
        return redirect()->back();
    }

    /**
     * Show blog.
     *
     * @return View
     */
    public function blog(): View
    {
        // Page title
        SEO::setTitle(trans('general.blog').' - '.config('settings.page_name').' » '.config('settings.sub_title'));

        $articles = Article::orderBy('created_at', 'desc')->get();

        return view('frontend.blog.overview', ['articles' => $articles]);
    }

    /**
     * Show Article.
     *
     * @param string $slug
     * @return View|RedirectResponse
     */
    public function article(string $slug): View|RedirectResponse
    {

        // Get listing id from slug string
        $article_id = ltrim(strrchr($slug, '-'), '-');

        $article = Article::find($article_id);

        // Check if slug is right
        $slug_check = Str::slug($article->slug).'-'.$article->id;

        // Redirect to correct slug link
        if ($slug_check !== $slug) {
            return redirect(url('blog/'.$slug_check));
        }

        // Page title
        SEO::setTitle($article->title.' » '.config('settings.page_name'));

        SEO::setDescription(mb_strimwidth(preg_replace('/<[\/\!]*?[^<>]*?>/si', '', $article->content), 0, 150, '...'));

        SEO::metatags()->addMeta('article:published_time', $article->created_at->toW3CString(), 'property');
        SEO::metatags()->addMeta('article:section', (isset($article->category->slug) ? $article->category->slug : 'uncategorized'), 'property');

        // Get image size for og
        if ($article->image_large) {
            $imgsize = getimagesize($article->image_large);
            SEO::opengraph()->addImage(['url' => $article->image_large, ['height' => $imgsize[1], 'width' => $imgsize[0]]]);
            // Twitter Card Image
            SEO::twitter()->setImage($article->image_large);
        }

        return view('frontend.blog.article', ['article' => $article]);
    }
}
