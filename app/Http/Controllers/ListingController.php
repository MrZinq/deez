<?php

namespace App\Http\Controllers;

use App\Models\Digital;
use App\Models\Game;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\Platform;
use App\Models\Wishlist;
use App\Notifications\PriceAlert;
use Artesaos\SEOTools\Facades\SEOTools as SEO;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Intervention\Image\Facades\Image;
use Prologue\Alerts\Facades\Alert;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Session;

class ListingController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Check for slug in overview and select right function.
     *
     * @param Request $request
     * @param string $slug
     * @return RedirectResponse|View
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function selectIndex(Request $request, string $slug): RedirectResponse|View
    {
        if (substr_count($slug, '-') >= 2) {
            return $this->show($slug);
        }

        return $this->index($request, $slug);
    }

    /**
     * Overview listings.
     *
     * @param Request $request
     * @param string|null $system
     * @return RedirectResponse|View
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(Request $request, string $system = null): RedirectResponse|View
    {
        // check for platform
        if ($system !== null) {
            $system = platform::where('acronym', $system)->first();

            // check if platform exist
            if ($system === null) {
                abort('404');
            }
        }

        // Check if user want to sort the listings by distance
        if (session()->has('listingsOrder') && session()->get('listingsOrder') === 'distance') {
            // get long / lat from user
            if (auth()->check() && (auth()->user()->location && auth()->user()->location->longitude && auth()->user()->location->latitude)) {
                $latitudeTo = auth()->user()->location->latitude;
                $longitudeTo = auth()->user()->location->longitude;
            } elseif (session()->has('latitude') && session()->has('longitude')) {
                $latitudeTo = session()->get('latitude');
                $longitudeTo = session()->get('longitude');
            } else {
                session()->remove('listingsOrder');
            }

            // If long / lat is set, get query with calculated distance
            if (isset($longitudeTo) && isset($latitudeTo)) {
                $listings = Listing::distanceto($latitudeTo, $longitudeTo);
            } else {
                $listings = Listing::query();
            }
        } else {
            $listings = Listing::query();
        }

        // Get all active listing
        // check if system is given
        if ($system === null) {
            $listings = $listings->where(function ($q) {
                $q->orWhere('status', null)->orWhere('status', 0);
            })->whereHas('user', function ($query) {
                $query->where('status', 1);
            });

            // Order - default order is created_at
            $listings_order = session()->has('listingsOrder') ? session()->get('listingsOrder') : 'created_at';

            // Order direction - default is asc
            if (session()->has('listingsOrderByDesc') && session()->get('listingsOrderByDesc')) {
                $listings = $listings->orderBy($listings_order);
            } else {
                $listings = $listings->orderBy($listings_order, 'desc');
            }

            // Platform filters
            if (session()->has('listingsPlatformFilter')) {
                $listings = $listings->whereHas('game', function ($q) {
                    $q->whereIn('platform_id', session()->get('listingsPlatformFilter'));
                });
            }

            // Option filters
            if (session()->has('listingsOptionFilter')) {
                foreach (session()->get('listingsOptionFilter') as $filter) {
                    if ($filter === 'digital') {
                        $listings = $listings->where($filter, '!=', null);
                    } else {
                        $listings = $listings->where($filter, true);
                    }
                }
            }

            // Page title
            SEO::setTitle(trans('general.title.listings_all', [
                'page_name' => config('settings.page_name'),
                'sub_title' => config('settings.sub_title'),
            ]));

            // Page description
            SEO::setDescription(trans('general.description.listings_all', [
                'listings_count'    => $listings->count(),
                'page_name'         => config('settings.page_name'),
                'sub_title'         => config('settings.sub_title'),
            ]));
        } else {
            $listings = $listings->whereHas('game', function ($query) use ($system) {
                $query->where('platform_id', $system->id);
            })->where(function ($q) {
                $q->orWhere('status', null)->orWhere('status', 0);
            })->whereHas('user', function ($query) {
                $query->where('status', 1);
            });

            // Order - default order is created_at
            $listings_order = session()->has('listingsOrder') ? session()->get('listingsOrder') : 'created_at';

            // Order direction - default is asc
            if (session()->has('listingsOrderByDesc') && session()->get('listingsOrderByDesc')) {
                $listings = $listings->orderBy($listings_order);
            } else {
                $listings = $listings->orderBy($listings_order, 'desc');
            }

            // Option filters
            if (session()->has('listingsOptionFilter')) {
                foreach (session()->get('listingsOptionFilter') as $filter) {
                    if ($filter === 'digital') {
                        $listings = $listings->where($filter, '!=', null);
                    } else {
                        $listings = $listings->where($filter, true);
                    }
                }
            }

            // Page title
            SEO::setTitle(trans('general.title.listings_platform', [
                'page_name' => config('settings.page_name'),
                'sub_title' => config('settings.sub_title'),
                'platform'  => $system->name, ]));

            // Page description
            SEO::setDescription(trans('general.description.listings_platform', [
                'listings_count'    => $listings->count(),
                'platform_name'     => $system->name,
                'page_name'         => config('settings.page_name'),
                'sub_title'         => config('settings.sub_title'),
            ]));
        }

        // Load game and user data and paginate the collection
        $listings = $listings->with('game', 'game.giantbomb', 'game.platform', 'user', 'user.location')->paginate(36);

        // Cloudflare SSL fix
        if (config('settings.ssl') || config('app.force_https')) {
            $listings->setPath('https://'.request()->getHttpHost().'/'.request()->path());
        }

        // Get the current page from the url if it's not set default to 1
        $page = $request->input('page', 0);

        // Redirect to first page if page from the get request don't exist
        if ($listings->lastPage() < $page) {
            if ($system === null) {
                return redirect('listings');
            } else {
                return redirect('listings/'.$system->acronym);
            }
        }

        // Check if ajax request
        if ($request->ajax()) {
            return view('frontend.listing.ajax.index', ['listings' => $listings,  'system' => $system]);
        }

        return view('frontend.listing.index', ['listings' => $listings,  'system' => $system]);
    }

    /**
     * Show listing details.
     *
     * @param string $slug
     * @return RedirectResponse|View
     */
    public function show(string $slug): RedirectResponse|View
    {
        // Get listing id from slug string
        $listing_id = ltrim(strrchr($slug, '-'), '-');
        $listing = Listing::with('game', 'user', 'game.platform')->find($listing_id);

        // Check if listing exists
        if (is_null($listing)) {
            abort('404');
        }

        // Check if slug is right
        $slug_check = Str::slug($listing->game->name).'-'.$listing->game->platform->acronym.'-'.Str::slug($listing->user->name).'-'.$listing->id;

        // Redirect to correct slug link
        if ($slug_check !== $slug) {
            return redirect(url('listings/'.$slug_check));
        }

        // Trade list
        if ($listing->trade_list) {
            $trade_list = Game::whereIn('id', array_keys(json_decode($listing->trade_list, true)))->with('giantbomb', 'platform')->get();
        } else {
            $trade_list = null;
        }

        // increment clicks
        $listing->increment('clicks');

        // SEO Data
        if ($listing->sell) {
            SEO::setTitle(trans('general.title.listing_buy', [
                'game_name' => $listing->game->name,
                'platform'  => $listing->game->platform->name,
                'price'     => $listing->price_formatted,
                'user_name' => $listing->user->name,
                'place'     => isset($listing->user->location) ? $listing->user->location->place : '',
            ]));
            SEO::setDescription(trans('general.description.listing_buy', [
                'game_name' => $listing->game->name,
                'platform'  => $listing->game->platform->name,
                'price'     => $listing->price_formatted,
                'user_name' => $listing->user->name,
                'place'     => isset($listing->user->location) ? $listing->user->location->place : '',
                'page_name' => config('settings.page_name'),
                'sub_title' => config('settings.sub_title'),
            ]));
        } else {
            SEO::setTitle(trans('general.title.listing_trade', [
                'game_name' => $listing->game->name,
                'platform'  => $listing->game->platform->name,
                'user_name' => $listing->user->name,
                'place'     => $listing->user->location->place,
            ]));
            SEO::setDescription(trans('general.description.listing_trade', [
                'game_name' => $listing->game->name,
                'platform'  => $listing->game->platform->name,
                'user_name' => $listing->user->name,
                'place'     => $listing->user->location->place,
                'page_name' => config('settings.page_name'),
                'sub_title' => config('settings.sub_title'),
            ]));
        }

        SEO::metatags()->addMeta('article:published_time', $listing->created_at->toW3CString(), 'property');
        SEO::metatags()->addMeta('article:section', $listing->game->platform->name, 'property');

        // Get image size for og
        if ($listing->game->image_cover) {
            try {
                $imgsize = getimagesize($listing->game->image_cover);
                SEO::opengraph()->addImage(['url' => $listing->game->image_cover, ['height' => $imgsize[1], 'width' => $imgsize[0]]]);
                // Twitter Card Image
                SEO::twitter()->setImage($listing->game->image_cover);
            } catch (Exception $e) {
                // Removed
            }
        }

        // Set back URL when logged user can edit listing
        if (auth()->check() && (auth()->user()->id === $listing->user_id || auth()->user()->can('edit_listings'))) {
            // Save back URL for finished form
            session()->flash('backUrl', $listing->url_slug);
        }

        $genre_id = $listing->game->genre_id;

        /*$similar_listings = Listing::with('game', 'user', 'game.giantbomb', 'game.platform')->whereHas('game', function ($query) use ($genre_id) {
    $query->where('genre_id', $genre_id);
})->get();*/

        return view('frontend.listing.show', ['game' => $listing->game, 'listing' => $listing, 'trade_list' => $trade_list]);
    }

    /**
     * Show listing create form.
     *
     * @return RedirectResponse|View
     */
    public function add(): RedirectResponse|View
    {
        // check if user account is active
        if (! auth()->user()->isActive()) {
            auth()->logout();

            return redirect('login')->with('error', trans('auth.deactivated'));
        }

        SEO::setTitle(trans('general.title.listing_add', ['page_name' => config('settings.page_name'), 'sub_title' => config('settings.sub_title')]));

        return view('frontend.listing.form', ['platforms' => Platform::all()]);
    }

    /**
     * Edit listing form.
     *
     * @param string $slug
     * @return RedirectResponse|View
     */
    public function editForm(string $slug): RedirectResponse|View
    {
        // get back url from session when listing is saved
        if (Session::has('backUrl')) {
            Session::keep('backUrl');
        }

        // Get listing id from slug string
        $listing_id = ltrim(strrchr($slug, '-'), '-');
        $listing = Listing::with('game', 'user', 'game.giantbomb', 'game.platform')->find($listing_id);

        // Check if listing exists
        if (is_null($listing)) {
            return redirect('/');
        }

        // Check if User can edit listing
        if (! (auth()->user()->id === $listing->user_id) && ! auth()->user()->can('edit_listings')) {
            abort('404');
        }

        // Check listing status
        if (! ($listing->status === 0 || is_null($listing->status))) {
            abort('404');
        }

        // Check if slug is right
        $slug_check = Str::slug($listing->game->name).'-'.$listing->game->platform->acronym.'-'.Str::slug($listing->user->name).'-'.$listing->id;

        // Redirect to correct slug link
        if ($slug_check !== $slug) {
            return redirect(url('listings/'.$slug_check.'/edit'));
        }

        // Check if image is saved in the listing_images table, which is needed since version 1.4.0
        if ($listing->picture) {
            $listing_image = $listing->images->where('filename', $listing->picture)->first();
            if (! isset($listing_image)) {
                $listing_image = new ListingImage;
                $listing_image->user_id = $listing->user_id;
                $listing_image->listing_id = $listing->id;
                $listing_image->filename = $listing->picture;
                $listing_image->default = true;
                $listing_image->order = '1';
                $listing_image->save();
            }
        }

        // Trade list
        if ($listing->trade_list) {
            $trade_list = Game::whereIn('id', array_keys(json_decode($listing->trade_list, true)))->with('giantbomb', 'platform')->get();
        } else {
            $trade_list = null;
        }

        // Page title
        SEO::setTitle(trans('general.title.listing_edit', ['game_name' => $listing->game->name, 'platform' => $listing->game->platform->name]));

        return view('frontend.listing.form', ['platforms' => Platform::all(), 'listing' => $listing, 'game' => $listing->game, 'trade_list' => $trade_list]);
    }

    /**
     * Add new listing form with game.
     *
     * @param string $slug
     * @return RedirectResponse|View
     */
    public function gameForm(string $slug): RedirectResponse|View
    {
        if (Session::has('backUrl')) {
            Session::keep('backUrl');
        }

        // Get listing id from slug string
        $game_id = ltrim(strrchr($slug, '-'), '-');
        $game = Game::with('giantbomb', 'platform')->find($game_id);

        // Check if listing exists
        if (is_null($game)) {
            abort('404');
        }

        // Check if slug is right
        $slug_check = Str::slug($game->name).'-'.$game->platform->acronym.'-'.$game->id;

        // Redirect to correct slug link
        if ($slug_check !== $slug) {
            return redirect(url('listings/'.$slug_check.'/new'));
        }

        SEO::setTitle(trans('general.title.listing_add_game', [
            'page_name' => config('settings.page_name'),
            'sub_title' => config('settings.sub_title'),
            'game_name' => $game->name,
            'platform'  => $game->platform->name,
        ]));

        return view('frontend.listing.form', ['platforms' => Platform::all(), 'game' => $game]);
    }

    /**
     * Save listing after edit.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function edit(Request $request): RedirectResponse
    {
        // check if user changed hidden inputs
        try {
            $request->merge([
                'game_id'    => decrypt($request->game_id),
                'listing_id' => decrypt($request->listing_id),
            ]);
        } catch (Exception $ex) {
            // show an alert message
            Alert::error('<i class="fa fa-times m-r-5"></i> Nothing saved. Do not try to change hidden inputs!')->flash();

            return ($url = Session::get('backUrl')) ? redirect()->to($url) : redirect()->back();
        }

        $this->validate($request, [
            'game_id' => 'required|exists:games,id',
            'listing_id' => 'required|exists:listings,id',
        ]);

        $listing = Listing::find($request->listing_id);

        // Check if game id is right
        if ($listing->game->id !== $request->game_id) {
            // show a alert message
            Alert::error('<i class="fa fa-times m-r-5"></i> Nothing saved. Do not try to change hidden inputs!')->flash();

            return ($url = Session::get('backUrl')) ? redirect()->to($url) : redirect()->back();
        }

        // Check if User can edit listing
        if (! (auth()->user()->id === $listing->user_id) && ! auth()->user()->can('edit_listings')) {
            abort('404');
        }

        // Check listing status
        if (! ($listing->status === 0 || is_null($listing->status))) {
            abort('404');
        }

        if ((int) $request->sell_status === 0 && (int) $request->trade_status === 0) {
            return redirect('/');
        }

        $listing = $this->processListing($request, $listing);

        // show a success message
        Alert::success('<i class="fa fa-save m-r-5"></i>'.trans('listings.alert.saved', ['game_name' => str_replace("'", '', $listing->game->name)]))
             ->flash();

        return ($url = Session::get('backUrl')) ? redirect()->to($url) : redirect()->back();
    }

    /**
     * Delete listing.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function delete(Request $request): RedirectResponse
    {
        // decrypt input
        $request->merge(['listing_id' => decrypt($request->get('listing_id'))]);

        $this->validate($request, [
            'listing_id' => 'required|exists:listings,id',
        ]);

        $listing = Listing::find($request->listing_id);

        if (! $listing) {
            abort('404');
        }

        // Check if logged in user can delete this listing
        if (! auth()->user()->can('edit_listings') && ! (auth()->user()->id === $listing->user_id)) {
            abort('404');
        }

        // Check status of listing
        if ($listing->status >= 1) {
            abort('404');
        }

        // Check if delete from listing
        if (url()->previous() === $listing->url_slug) {
            $redirect_back = false;
        } else {
            $redirect_back = true;
        }

        // Remove images
        if (count($listing->images) > 0) {
            foreach ($listing->images as $image) {
                // Remove file image
                $destination_path = 'public/listings';
                $disk = 'local';
                Storage::disk($disk)->delete($destination_path.'/'.$image->filename);

                // Delete database entry
                $image->delete();
            }
            $listing->picture = null;
            $listing->save();
        }

        // delete listing
        $listing->delete();

        // show a success message
        Alert::error('<i class="fa fa-trash m-r-5"></i>'.trans('listings.alert.deleted', ['game_name' => str_replace("'", '', $listing->game->name)]))
             ->flash();

        return $redirect_back ? redirect()->back() : redirect()->to('/');
    }

    /**
     * Store new listing.
     *
     * @param Request $request
     * @return RedirectResponse|Listing
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse|Listing
    {
        $this->validate($request, [
            'game_id' => 'required|exists:games,id',
        ]);

        // check if sell and trade is deactivated
        if ((int) $request->sell_status === 0 && (int) $request->trade_status === 0) {
            return ($url = Session::get('backUrl')) ? redirect()->to($url) : redirect()->back();
        }

        // Check if user set location
        if (! auth()->user()->location) {
            return ($url = Session::get('backUrl')) ? redirect()->to($url) : redirect()->back();
        }

        $listing = $this->processListing($request);

        // show a success message
        Alert::success('<i class="fa fa-plus m-r-5"></i>'.trans('listings.alert.created', ['game_name' => str_replace("'", '', $listing->game->name)]))
             ->flash();

        // Check if request was sent through ajax
        if (request()->ajax()) {
            return $listing;
        }

        return redirect($listing->url_slug);
    }

    /**
     * Sort listings.
     *
     * @param string $order
     * @param string|null $desc
     * @return mixed
     */
    public function order(string $order, string $desc = null): RedirectResponse
    {
        if ($order === 'distance' || $order === 'created_at' || $order === 'price') {
            session()->put('listingsOrder', $order);
        } else {
            session()->remove('listingsOrder');
        }

        session()->put('listingsOrderByDesc', $desc === 'desc');

        return redirect(url()->current() === url()->previous() ? url('/') : url()->previous());
    }

    /**
     * Filter listings.
     *
     * @param Request $request
     * @return string
     */
    public function filter(Request $request): String
    {
        session()->put('listingsPlatformFilter', $request->get('platformIds'));
        session()->put('listingsOptionFilter', $request->get('options'));

        return url()->current() === url()->previous() ? url('/') : strtok(url()->previous(), '?');
    }

    /**
     * Remove filter for listings.
     *
     * @return string
     */
    public function filterRemove(): String
    {
        session()->remove('listingsPlatformFilter');
        session()->remove('listingsOptionFilter');

        return redirect(url()->current() === url()->previous() ? url('/') : url()->previous());
    }

    /**
     * Display all images.
     *
     * @param int $id
     * @return Collection
     */
    public function images(int $id): Collection
    {
        // Check if request was sent through ajax
        if (! request()->ajax()) {
            abort(404);
        }

        return Listing::find($id)->images;
    }

    /**
     * Change the order of the listing images.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function imagesSort(Request $request, int $id): JsonResponse
    {
        // Ignore user aborts
        ignore_user_abort(true);

        // Get event
        $listing = Listing::find($id);

        // Order variable
        $order = 1;

        // Get through all images and set the order
        foreach (json_decode($request->order) as $filename) {
            // Get image
            $image = ListingImage::where('filename', $filename)->first();

            // Change order image (if exists)
            if ($image) {
                // Set the new order
                $image->order = $order;
                // Check if It's the first image and change the default event image
                if ($order === 1) {
                    $image->default = 1;
                    $listing->picture = $image->filename;
                } else {
                    $image->default = 0;
                }
                $listing->save();
                $image->save();
            }
            $order++;
        }

        // Return a success response
        return response()->json('success', 200);
    }

    /**
     * Uploads image.
     *
     * @param Request $request
     * @param int|null $id
     * @return JsonResponse
     */
    public function imagesUpload(Request $request, int $id = null): JsonResponse
    {
        // Ignore user aborts
        ignore_user_abort(true);

        $listing = Listing::find($id ?? $request->get('listing_id'));

        if ($listing) {
            $order = $request->order;

            $extension = 'jpg';
            $newfilename = time().$order.'-'.$listing->id.'.'.$extension;
            $destination_path = 'public/listings';

            $img = Image::make($request->file->path());
            $disk = 'local';

            Storage::disk($disk)->put($destination_path.'/'.$newfilename, $img->stream());

            // Start order from 1 instead of 0
            $order += 1;

            $listing_image = new ListingImage;
            $listing_image->user_id = auth()->id();
            $listing_image->listing_id = $listing->id;
            $listing_image->filename = $newfilename;
            $listing_image->order = $order;

            if ($order === 1) {
                $listing_image->default = 1;
                $listing->picture = $newfilename;
                $listing->save();
            }

            $listing_image->save();

            return response()->json($listing_image);
        } else {
            return response()->json('error', 404);
        }
    }

    /**
     * Remove an image file and the entry in the database.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function imagesRemove(Request $request, int $id): JsonResponse
    {
        // Ignore user aborts
        ignore_user_abort(true);

        // Get image item
        $image = ListingImage::where('filename', $request->filename)->first();

        // Check if image is default image and remove it
        if ($image->default) {
            $listing = Listing::find($image->listing_id);
            $listing->picture = null;
            $listing->save();
        }

        // Remove file image
        $destination_path = 'public/listings';
        $disk = 'local';
        Storage::disk($disk)->delete($destination_path.'/'.$request->filename);

        // Delete database entry
        $image->delete();

        // Return a success response
        return response()->json('success', 200);
    }

    /**
     * Process listing.
     *
     * @param Request $request
     * @param Listing|null $listing
     * @return Listing
     */
    private function processListing(Request $request, Listing $listing = null): Listing
    {
        $datapost = $request->all();

        $datapost['delivery'] = ($request->has('delivery')) ? 1 : 0;
        $datapost['pickup'] = ($request->has('pickup')) ? 1 : 0;

        $datapost['digital'] = ($request->has('digital')) ? 1 : 0;
        $datapost['limited'] = ($request->has('limited')) ? 1 : 0;

        if ($datapost['limited'] === 1 && $request->input('limited_name') !== '') {
            $limited_edition = $datapost['limited_name'];
        }

        // check if delivery or pickup is selected
        if ($datapost['delivery'] === 0 && $datapost['pickup'] === 0 && ! config('settings.digital_downloads_only')) {
            ($url = Session::get('backUrl')) ? redirect()->to($url) : redirect()->back();
        }

        if (isset($datapost['trade_list'])) {
            // Save Trade List data to games_trade for game overview
            foreach ($datapost['trade_list'] as $trade_game) {
                // filter price
                $add_price = filter_var($trade_game['price'], FILTER_SANITIZE_NUMBER_INT);
                // check if listing game is in trade list
                if ($trade_game['id'] !== $request->game_id) {
                    $data_trade[$trade_game['id']] = [
                        'game_id'    => $trade_game['id'],
                        'price'      => ! empty($add_price) ? abs(filter_var($add_price, FILTER_SANITIZE_NUMBER_INT)) : '0',
                        'price_type' => ! empty($add_price) ? $trade_game['price_type'] : 'none',
                    ];
                }
            }
            $trade_list = isset($data_trade) ? json_encode($data_trade) : null;
        } else {
            $trade_list = null;
            $trade_status = 0;
        }

        // create new listing
        $listing = $listing ?? new Listing;

        // General data
        $listing->user_id = auth()->user()->id;
        $listing->game_id = $request->game_id;

        // Listing details
        $listing->limited_edition = isset($limited_edition) ? $limited_edition : null;
        $listing->condition = $request->condition;
        // Check if digital downloads only is enabled
        if (config('settings.digital_downloads_only')) {
            $listing->pickup = 0;
            $listing->delivery = 1;
            $listing->delivery_price = null;
        } else {
            $listing->pickup = $request->pickup ? 1 : 0;
            $listing->delivery = $request->delivery ? 1 : 0;
            $listing->delivery_price = $request->delivery ? $request->delivery_price : null;
        }
        $listing->description = $request->description;

        // check if digital distributor exists
        $digital_distributor = Digital::find($request->digital_distributor);

        // Digital Download
        if (($datapost['digital'] === 1 && $digital_distributor) || config('settings.digital_downloads_only') && $digital_distributor) {
            $listing->digital = $digital_distributor->id;
            $listing->condition = null;
        } else {
            if ($listing->condition === 0) {
                $listing->condition = 5;
            }
            $listing->digital = null;
        }

        // Sell data
        $listing->sell_negotiate = (int) $request->sell_status === 1 ? ($request->sell_negotiate ? 1 : 0) : 0;
        $listing->sell = (int) $request->sell_status;
        $listing->price = (int) $request->sell_status === 1 ? $request->price : null;

        // Trade data
        $listing->trade_negotiate = (int) $request->trade_status === 1 ? ($request->trade_negotiate ? 1 : 0) : 0;
        $listing->trade = $trade_list ? (int) $request->trade_status : ((int) $request->trade_status && $request->trade_negotiate ? 1 : 0);
        $listing->trade_list = (int) $request->trade_status === 1 ? $trade_list : null;

        // Payment data
        $listing->payment = (int) $request->sell_status ? ($listing->delivery && ($request->enable_payment || config('settings.payment_force')) ? 1 : 0) : 0;

        // stop saving when sell and trade status is still 0
        if (! $listing->sell && ! $listing->trade) {
            ($url = Session::get('backUrl')) ? redirect()->to($url) : redirect()->back();
        }

        $listing->save();

        // create trade list for game
        if ($listing->trade_list) {
            foreach (json_decode($listing->trade_list) as $trade_game) {
                $trade_synch_list[$trade_game->game_id] = ['listing_game_id' => $listing->game_id, 'price' => $trade_game->price, 'price_type' => $trade_game->price_type];
            }
            $listing->tradegames()->sync($trade_synch_list);
        } else {
            $listing->tradegames()->detach();
        }

        // Send price alerts
        // Get all wishlists
        $wishlists = Wishlist::where('game_id', $listing->game_id)->where('user_id', '!=', $listing->user_id)->get();

        foreach ($wishlists as $wishlist) {
            if (! isset($wishlist->max_price) || ($listing->sell && $wishlist->max_price >= $listing->price)) {
                $check_array = [
                    'listing_id' => $listing->id,
                    'wishlist_id' => $wishlist->id,
                ];

                // get latest price alert for the user
                $notification_check = $wishlist->user->notifications()->where('data', json_encode($check_array))->first();

                // Check if user already get a price alert for this listing
                if (! $notification_check) {
                    // Send price alert to user
                    $wishlist->user->notify(new PriceAlert($listing, $wishlist));
                }
            }
        }

        return $listing;
    }
}
