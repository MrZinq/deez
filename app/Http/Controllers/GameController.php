<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Genre;
use App\Models\Giantbomb;
use App\Models\Platform;
use Artesaos\SEOTools\Facades\SEOTools as SEO;
use DBorsatto\GiantBomb\Configuration;
use DBorsatto\GiantBomb\Exception\ModelException;
use DBorsatto\GiantBomb\Exception\SdkException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Session;
use Wiledia\Searchy\Facades\Searchy;

class GameController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Index all games.
     *
     * @param Request $request
     * @return RedirectResponse|View
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(Request $request): RedirectResponse|View
    {
        // Games query
        $games = Game::query();

        // Order - default order is created_at
        $games_order = session()->has('gamesOrder') ? session()->get('gamesOrder') : 'release_date';

        // Platform filters
        if (session()->has('listingsPlatformFilter')) {
            $games = $games->whereIn('platform_id', session()->get('listingsPlatformFilter'));
        }

        // Load other tables
        $games = $games->with('platform', 'giantbomb', 'listingsCount', 'wishlistCount', 'metacritic');

        // Order direction - default is asc
        $gamesOrderDirection = session()->has('gamesOrderByDesc') && session()->get('gamesOrderByDesc') ? 'asc' : 'desc';

        // Games order by
        $games = match ($games_order) {
            // Order by metascore
            'metascore'  => $games->join('games_metacritic', 'games.id', 'games_metacritic.game_id')
                                  ->orderBy('games_metacritic.score', $gamesOrderDirection)
                                  ->select('games.*'),
            // Order by listings count
            'listings'   => $games->withCount('listings')
                                  ->orderBy('listings_count', $gamesOrderDirection),
            // Order by popularity
            'popularity' => $games->withCount('heartbeat')
                                  ->orderBy('heartbeat_count', $gamesOrderDirection),
            // default order
            default      => $games->orderBy($games_order, $gamesOrderDirection)
        };

        // Paginate games results
        $games = $games->paginate('36');

        // Cloudflare SSL fix
        if (config('settings.ssl') || config('app.force_https')) {
            $games->setPath('https://'.$request->getHttpHost().'/'.$request->path());
        }

        // Get the current page from the url if it's not set default to 1
        $page = $request->input('page', 0);

        // Redirect to first page if page from the get request don't exist
        if ($games->lastPage() < $page) {
            return redirect('games');
        }

        // Page title
        SEO::setTitle(trans('general.title.games_all', [
            'page_name' => config('settings.page_name'),
            'sub_title' => config('settings.sub_title'),
        ]));

        // Page description
        SEO::setDescription(trans('general.description.games_all', [
            'games_count' => $games->total(),
            'page_name'   => config('settings.page_name'),
            'sub_title'   => config('settings.sub_title'),
        ]));

        // Check if ajax request
        if ($request->ajax()) {
            return view('frontend.game.ajax.index', ['games' => $games]);
        }

        return view('frontend.game.index', ['games' => $games]);
    }

    /**
     * Display game infos with all listing.
     *
     * @param string $slug
     * @return RedirectResponse|View
     */
    public function show(string $slug): RedirectResponse|View
    {
        // Get game id from slug string
        $game_id = ltrim(strrchr($slug, '-'), '-');
        $game = Game::with('listings')->find($game_id);

        // Check if game exists
        if (is_null($game)) {
            abort('404');
        }

        // Check if slug is right
        $slug_check = \Illuminate\Support\Str::slug($game->name).'-'.$game->platform->acronym.'-'.$game->id;

        // Redirect to correct slug link
        if ($slug_check !== $slug) {
            return redirect(url('games/'.$slug_check));
        }

        // Page title & description
        SEO::setTitle(trans('general.title.game', ['game_name' => $game->name, 'platform' => $game->platform->name, 'page_name' => config('settings.page_name')]));
        SEO::setDescription((strlen($game->description) > 147) ? substr($game->description, 0, 147).'...' : $game->description);

        // Get different platforms for the game
        $different_platforms = Game::where('giantbomb_id', '!=', '0')
                                   ->where('giantbomb_id', $game->giantbomb_id)
                                   ->where('id', '!=', $game->id)
                                   ->where('platform_id', '!=', $game->platform_id)
                                   ->with('platform')->get();

        // Get image size for og
        if ($game->image_cover) {
            // Check if image is corrupted
            try {
                $imgsize = getimagesize($game->image_cover);
                SEO::opengraph()->addImage(['url' => $game->image_cover, ['height' => $imgsize[1], 'width' => $imgsize[0]]]);
                // Twitter Card Image
                SEO::twitter()->setImage($game->image_cover);
            } catch (\Exception $e) {
                // Delete corrupted image
                // $disk = "local";
                // \Storage::disk($disk)->delete('/public/games/' . $game->cover );
                // $game->cover = null;
                // $game->save();
            }
        }

        return view('frontend.game.show', ['game' => $game, 'different_platforms' => $different_platforms]);
    }

    /**
     * Get media (images & videos) tab in game and listing overview.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|View
     */
    public function showMedia(Request $request, int $id): RedirectResponse|View
    {
        $game = Game::with('giantbomb')->find($id);

        // Accept only ajax requests
        if (! $request->ajax()) {
            // redirect to game if no AJAX request
            if ($game) {
                return redirect(url($game->url_slug.'#!media'));
            }

            abort('404');
        }

        // Check if game exist
        if (! $game) {
            abort('404');
        }

        // Get images from giantbomb
        $images = $game->giantbomb_id !== 0 ? json_decode($game->giantbomb->images) : null;
        $videos = $game->giantbomb_id !== 0 ? json_decode($game->giantbomb->videos) : null;

        // don't lose backUrl session if one is set
        if (Session::has('backUrl')) {
            Session::keep('backUrl');
        }

        return view('frontend.game.showMedia', ['game' => $game, 'images' => $images, 'videos' => $videos]);
    }

    /**
     * Get available trade games for the specific game in the tab in game overview.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|View
     */
    public function showTrade(Request $request, int $id): RedirectResponse|View
    {
        $game = Game::find($id);

        // Accept only ajax requests
        if (! $request->ajax()) {
            // redirect to game if no AJAX request
            if ($game) {
                return redirect(url($game->url_slug.'#!trade'));
            }

            abort('404');
        }

        // Check if game exist
        if (! $game) {
            abort('404');
        }

        // help to check if trade games was removed in the next step
        $removed_games = false;

        // Remove not active listings
        foreach ($game->tradegames as $listing) {
            // check if listing is removed or not active
            if ($listing->status === 1 || $listing->status === 2 || $listing->deleted_at) {
                \DB::table('game_trade')->where('listing_id', $listing->id)->where('game_id', $game->id)->delete();
                $removed_games = true;
            }
        }

        if ($removed_games) {
            // Refresh game model
            $game = $game->fresh();
        }

        return view('frontend.game.showTrade', ['tradegames' => $game->tradegames]);
    }

    /**
     * Form for adding a new game.
     *
     * @return View
     */
    public function add(): View
    {
        // Check if user can add games to the system
        if (! config('settings.user_add_item') && ! (auth()->user()->can('edit_games'))) {
            abort(404);
        }

        // Page title
        SEO::setTitle(trans('general.title.game_add', ['page_name' => config('settings.page_name')]));

        return view('frontend.game.add', ['platforms' => Platform::all()]);
    }

    /**
     * Search games.
     *
     * @param Request $request
     * @param string $value
     * @return View
     */
    public function search(Request $request, string $value): View
    {
        // search for games
        $games = Game::hydrate(Searchy::games('name', 'tags')->query($value)->get()->toArray());

        $games->load('platform', 'giantbomb');

        // Get the current page from the url if it's not set default to 1
        $page = $request->get('page', 1);

        // Number of items per page
        $perPage = 36;

        // Start displaying items from this number;
        $offSet = ($page * $perPage) - $perPage; // Start displaying items from this number

        // Get only the items you need using array_slice (only get 10 items since that's what you need)
        //$itemsForCurrentPage = array_slice($deals_query->toArray(), $offSet, $perPage, true);

        // Page title
        SEO::setTitle(trans('general.title.search_result', [
            'page_name' => config('settings.page_name'),
            'sub_title' => config('settings.sub_title'),
            'value'     => $value,
        ]));

        // and return to typeahead
        return view('frontend.game.searchindex', [
            'games' => new LengthAwarePaginator($games->forPage($page, $perPage), count($games), $perPage, $page, ['path' => $request->url()]), 'value' => $value,
        ]);
    }

    /**
     * Metacritic api search.
     *
     * @param Request $request
     * @return View
     * @throws GuzzleException
     */
    public function searchApi(Request $request): View
    {
        // Accept only ajax requests
        if (! $request->ajax()) {
            abort('404');
        }

        $client = new Client();

        $searchParam = $request->get('search_param', 'all');
        $game = $request->get('game');
        $page = $request->get('page', 1);

        // search with metacritic api
        $res = $client->request(
            method:'GET',
            uri: url('metacritic/search/game?platform='.$searchParam.'&title='.$game.'&page='.$page)
        );

        $json_results = json_decode($res->getBody());

        $platforms = Platform::whereIn('acronym', array_column($json_results->results, 'platform'))->get();

        // and return view to ajax
        return view('frontend.game.api.search', [
            'json_results' => $json_results->results,
            'pages'        => $json_results->pages,
            'current_page' => $json_results->current_page,
            'platforms'    => $platforms,
            'value'        => $request->game,
            'trade_search' => $request->trade_search,
        ]);
    }

    /**
     * Search with json response.
     *
     * @param Request $request
     * @param string $value
     * @return JsonResponse
     */
    public function searchJson(Request $request, string $value): JsonResponse
    {
        // Accept only ajax requests
        if (! $request->ajax()) {
            abort('404');
        }

        $games = Game::hydrate(
            Searchy::games('name', 'tags')
                   ->query($value)
                   ->getQuery()
                   ->limit(10)
                   ->get()
                   ->toArray()
        );

        $games->load('platform', 'giantbomb', 'listingsCount', 'cheapestListing');

        $data = [];

        foreach ($games as $game) {
            $image_name = substr($game->cover, 0, -4);
            $data[' '.$game->id]['id'] = $game->id;
            $data[' '.$game->id]['name'] = $game->name;
            $data[' '.$game->id]['pic'] = $game->image_square_tiny;
            $data[' '.$game->id]['platform_name'] = $game->platform->name;
            $data[' '.$game->id]['platform_color'] = $game->platform->color;
            $data[' '.$game->id]['platform_light'] = $game->platform->cover_is_light;
            $data[' '.$game->id]['platform_acronym'] = $game->platform->acronym;
            $data[' '.$game->id]['platform_digital'] = $game->platform->digitals->count() > 0 ? true : false;
            $data[' '.$game->id]['listings'] = $game->listings_count;
            $data[' '.$game->id]['release_year'] = $game->release_date ? $game->release_date->format('Y') : 'unknown';
            $data[' '.$game->id]['cheapest_listing'] = $game->cheapest_listing;
            $data[' '.$game->id]['url'] = $game->url_slug;
            $data[' '.$game->id]['avgprice'] = $game->getAveragePrice();
            $data[' '.$game->id]['avgprice_string'] = trans('listings.form.sell.avgprice', ['game_name' => $game->name, 'avgprice' => $game->getAveragePrice()]);
        }

        // and return to typeahead
        return response()->json($data);
    }

    /**
     * Add new game to database.
     *
     * @param Request $request
     * @param bool $json
     * @return string
     * @throws ModelException
     * @throws SdkException
     * @throws GuzzleException
     */
    public function addgame(Request $request, bool $json = null): String
    {
        // Accept only ajax requests
        if (! $request->ajax()) {
            abort('404');
        }

        // Ignore user aborts and allow the script
        // to run forever
        ignore_user_abort(true);
        // set_time_limit(0);

        // Check and get platform data
        $platform = Platform::where('acronym', $request->platform)->first();

        // get all genres
        $genres = Genre::all();

        if ($platform) {
            $platform_id = $platform->id;
        } else {
            $platform_id = 0;
        }

        try {
            // New request to mc api
            $client = new Client();
            $res = $client->request('GET', url('metacritic/details?url=game/'.$request->mc_platform.'/'.urlencode($request->mc_game)));
        } catch (\Exception $e) {
            // show a error message
            \Alert::error('<i class="fa fa-times m-r-5"></i> API Error!')->flash();

            return url()->previous();
        }

        // decode results
        $json_results = json_decode($res->getBody())->result;

        // abort and return 404 on error
        if (! $json_results) {
            return urlencode($request->value);
        }

        // check if release is unknown
        $unknown_release = $json_results->rlsdate === '1970-01-01';

        // create new game and add data
        $game = new Game;

        $game->name = $json_results->name;
        $game->platform_id = $platform_id;
        $game->publisher = $json_results->publisher;
        $game->developer = $json_results->developer;
        $game->release_date = $unknown_release ? (date('Y') + 1).'-01-01' : $json_results->rlsdate;

        // Save game in database
        $game->save();

        // get game ID
        $game_id = $game->id;

        try {
            // JSON Data for new metacritic for SQL Insert
            $data_meta = [
                'game_id'      => $game_id,
                'name'         => $json_results->name,
                'score'        => isset($json_results->score) && $json_results->score !== '' ? $json_results->score : null,
                'userscore'    => isset($json_results->userscore) ? $json_results->userscore * 10 : null,
                'thumbnail'    => $json_results->thumbnail,
                'summary'      => $json_results->summary,
                'platform'     => $json_results->platform,
                'genre'        => json_encode($json_results->genre),
                'publisher'    => $json_results->publisher,
                'developer'    => $json_results->developer,
                'rating'       => $json_results->rating,
                'release_date' => $unknown_release ? (date('Y') + 1).'-01-01' : $json_results->rlsdate,
                'url'          => $json_results->url,
            ];

            // Insert Data in Table
            $metacritic_id = \DB::table('games_metacritic')->insertGetId($data_meta);
        } catch (\Exception $e) {
            // Delete game
            $game->forceDelete();
            // show a error message
            \Alert::error('<i class="fa fa-times m-r-5"></i> MC Error!')->flash();

            return url()->previous();
        }

        // START GIANTBOMB
        $metacritic_name = \DB::table('games_metacritic')->where('game_id', $game_id)->pluck('name');

        $apiKey = str_replace(' ', '', config('settings.giantbomb_key'));

        try {
            // Create a Config object and pass it to the Client
            $config = new Configuration($apiKey);
            $client = new \DBorsatto\GiantBomb\Client($config);
            $results = $client->search('"'.$metacritic_name.'"', 'game');
        } catch (\Exception $e) {
            // Delete game
            $game->forceDelete();
            // show a error message
            \Alert::error('<i class="fa fa-times m-r-5"></i> GiantBomb Error! Wrong API Key?')->flash();

            return url()->previous();
        }

        if (count($results) > 0) {
            // Check Releaseyear
            $game_number = 0;
            $metacritic_year = $unknown_release ? date('Y') : substr($json_results->rlsdate, 0, 4);

            do {
                if (isset($results[$game_number])) {
                    if ($unknown_release) {
                        $giantbomb_year = substr($results[$game_number]->original_release_date, 0, 4);
                        $giantbomb_added = substr($results[$game_number]->date_added, 0, 4);

                        // Check for release date
                        if ($giantbomb_year >= $metacritic_year || $results[$game_number]->expected_release_year >= $metacritic_year || $giantbomb_added >= $metacritic_year - 1) {
                            break;
                        } else {
                            $game_number++;
                        }
                    } else {
                        $giantbomb_year = substr($results[$game_number]->original_release_date, 0, 4);
                        $giantbomb_added = substr($results[$game_number]->date_added, 0, 4);

                        // Check if name is exact the same
                        if (strcmp($results[$game_number]->name, $json_results->name) === 0) {
                            break;
                        }

                        // Check for release date
                        if ($giantbomb_year === $metacritic_year || $results[$game_number]->expected_release_year === $metacritic_year || $giantbomb_added === $metacritic_year) {
                            break;
                        } else {
                            $game_number++;
                        }
                    }
                } else {
                    break;
                }
            } while (true);

            if (isset($results[$game_number])) {
                $gameid = '3030-'.$results[$game_number]->id;

                // Check if giantbomb data already exists
                $giantbomb_check = \DB::table('games_giantbomb')->where('id', $results[$game_number]->id)->first();

                if (! $giantbomb_check) {
                    $gamegb = $client->findWithResourceID('Game', $gameid);

                    $images = $gamegb->get('images');
                    $cover_image = $gamegb->get('image');
                    $videos = $gamegb->get('videos');

                    // Get genres if exists
                    try {
                        $genres = $gamegb->get('genres');
                    } catch (\InvalidArgumentException $ex) {
                        $genres = null;
                    }

                    $new_videos = [];

                    if ($genres) {
                        $new_genres = [];

                        // Genres add
                        foreach ($genres as $genre) {
                            array_push($new_genres, $genre['name']);
                            $check_genre = Genre::where('name', $genre['name'])->first();
                            if (! $check_genre) {
                                if (config('settings.automatic_genres')) {
                                    $new_genre = new Genre;
                                    $new_genre->name = $genre['name'];
                                    $new_genre->save();
                                    $game->genre_id = $new_genre->id;
                                }
                            } else {
                                $game->genre_id = $check_genre->id;
                            }
                        }
                    }

                    // Image add
                    $new_images = $this->getGiantBombImages(images: $images ?? []);

                    // Video Add

                    $video_help = 0;

                    foreach ($videos as $video_api) {
                        if ($video_help === 20) {
                            break;
                        }

                        if (! str_starts_with($video_api['name'], "Bombin' the A.M.")) {
                            try {
                                $video = $client->findWithResourceID('Video', $video_api['id']);

                                // We only want to save YouTube videos
                                if ($video->get('youtube_id') === '') {
                                    continue;
                                }

                                $new_videos[$video_help]['name'] = $video_api['name'];
                                $new_videos[$video_help]['api_id'] = substr($video_api['api_detail_url'], 36, -1);

                                $new_videos[$video_help]['id'] = $video->get('id');
                                $new_videos[$video_help]['length_seconds'] = $video->get('length_seconds');
                                $new_videos[$video_help]['deck'] = $video->get('deck');
                                $new_videos[$video_help]['video_type'] = $video->get('video_type');
                                $new_videos[$video_help]['youtube_id'] = $video->get('youtube_id');

                                $video_image = $video->get('image');

                                $imageParts = explode('/', ($video_image['icon_url']));
                                $imageName = implode('/', array_slice($imageParts, -3, 3, true));

                                $new_videos[$video_help]['image'] = $imageName;

                                $video_help++;
                            } catch (\Exception $e) {
                                // catch code
                            }
                        }
                    }

                    // PEGI Rating and get all ratings
                    $ratings = $gamegb->get('original_game_rating');

                    $all_ratings = [];

                    if ($ratings !== '') {
                        $pegi = 0;

                        foreach ($ratings ?? [] as $rating) {
                            // For array
                            array_push($all_ratings, $rating['name']);

                            // For database
                            $rating_name = substr($rating['name'], 0, 4);

                            if ($rating_name === 'PEGI') {
                                $pegi = substr($rating['name'], 6, -1);
                            }
                        }

                        if ($pegi !== 0) {
                            $game->pegi = $pegi;
                        }
                    }

                    // Tags add
                    if ($gamegb->get('aliases') !== '') {
                        $game->tags = $gamegb->get('aliases');
                    }

                    // Data for SQL Insert
                    $data = [
                        'id' => $results[$game_number]->id,
                        'name' => $results[$game_number]->name,
                        'summary' => $results[$game_number]->deck,
                        'genres' => $genres ? json_encode($new_genres) : null,
                        'image' => substr($cover_image['icon_url'], 50),
                        'images' => json_encode($new_images),
                        'videos' => json_encode($new_videos),
                        'ratings' => json_encode($all_ratings),
                    ];

                    // Insert Data in Table
                    \DB::table('games_giantbomb')->insert($data);

                    // Image Beta
                    $extension = 'jpg';
                    $newfilename = time().'-'.$game_id.'.'.$extension;
                    $disk = 'local';
                    $destination_path = 'public/games';

                    $image_client = new Client();
                    $image = $image_client->request('GET', $cover_image['super_url']);

                    // 2. Store the image on disk.
                    \Storage::disk($disk)->put($destination_path.'/'.$newfilename, $image->getBody()->getContents());

                    // Update Game Data with Giantbomb Info
                    $game->giantbomb_id = $results[$game_number]->id;
                    $game->cover = $newfilename;
                    $game->description = $results[$game_number]->deck;
                    $game->save();
                } else {
                    $giantbomb_check_game = Game::where('giantbomb_id', $giantbomb_check->id)->first();

                    // get genre from giantbomb
                    if ($giantbomb_check->genres) {
                        $giantbomb_genres = json_decode($giantbomb_check->genres);
                        $db_genre = Genre::where('name', $giantbomb_genres[0])->first();
                        if ($db_genre) {
                            $game->genre_id = $db_genre->id;
                        } else {
                            if (config('settings.automatic_genres')) {
                                $new_genre = new Genre;
                                $new_genre->name = $giantbomb_genres[0]['name'];
                                $new_genre->save();
                                $game->genre_id = $new_genre->id;
                            }
                        }
                    }

                    if ($giantbomb_check_game->cover) {
                        // Image Beta
                        $extension = 'jpg';
                        $newfilename = time().'-'.$game_id.'.'.$extension;
                        $disk = 'local';
                        $destination_path = 'public/games';

                        $image_client = new Client();
                        $image = $image_client->request('GET', $giantbomb_check_game->image_cover);

                        // 2. Store the image on disk.
                        \Storage::disk($disk)->put($destination_path.'/'.$newfilename, $image->getBody()->getContents());

                        $game->cover = $newfilename;
                    }

                    // get game with giantbomb id for tags and PEGI, when game exists
                    $giantbomb_game = Game::where('giantbomb_id', $giantbomb_check->id)->first();
                    if ($giantbomb_game) {
                        // Tags add
                        if ($giantbomb_game->tags) {
                            $game->tags = $giantbomb_game->tags;
                        }

                        // Pegi add
                        if ($giantbomb_game->pegi) {
                            $game->pegi = $giantbomb_game->pegi;
                        }
                    }

                    // Get data from giantbomb and save to new game
                    $game->giantbomb_id = $giantbomb_check->id;
                    $game->description = $giantbomb_check->summary;
                    $game->save();
                }
            }
        }

        if (is_null($json)) {
            // output url from game
            return url($game->url_slug);
        } else {
            // output game data as json
            $data = [];

            $data['id'] = $game->id;
            $data['name'] = $game->name;
            $data['pic'] = $game->image_square_tiny;
            $data['platform_name'] = $game->platform->name;
            $data['platform_color'] = $game->platform->color;
            $data['listings'] = $game->listings_count;
            $data['cheapest_listing'] = $game->cheapest_listing;
            $data['url'] = $game->url_slug;

            return response()->json($data);
        }
    }

    /**
     * Refresh metacritic data for game.
     *
     * @param int $game_id
     * @return RedirectResponse
     * @throws GuzzleException
     */
    public function refresh_metacritic(int $game_id): RedirectResponse
    {
        $game = Game::with('listings', 'metacritic')->find($game_id);

        // Check if game exists
        if (is_null($game)) {
            abort('404');
        }

        // Check if logged in
        if (! (auth()->check())) {
            return redirect(url('login'));
        }

        // Check if user can edit games
        if (! (auth()->user()->can('edit_games'))) {
            abort(403);
        }

        // Ignore user aborts and allow the script
        // to run forever
        ignore_user_abort(true);
        // set_time_limit(0);

        // New request to mc api
        $client = new Client();

        // Explore Metacritic URL parts
        $metacriticUrlParts = explode('/', $game->metacritic->url);

        $metacriticGameName = end($metacriticUrlParts);
        $metacriticPlatformName = prev($metacriticUrlParts);

        $res = $client->request('GET', url('metacritic/details?url=game/'.$metacriticPlatformName.'/'.$metacriticGameName));

        // decode results
        $json_results = json_decode($res->getBody())->result;

        // abort and return 404 on error
        if (! $json_results) {
            abort('404');
        }

        // JSON Data for new metacritic for SQL Insert
        $data_meta = [
            'game_id' => $game->id,
            'name' => $json_results->name,
            'score' => isset($json_results->score) && $json_results->score !== '' ? $json_results->score : null,
            'userscore' =>  isset($json_results->userscore) ? $json_results->userscore * 10 : null,
            'thumbnail' => $json_results->thumbnail,
            'summary' => $json_results->summary,
            'platform' => $json_results->platform,
            'genre' => json_encode($json_results->genre),
            'publisher' => $json_results->publisher,
            'developer' => $json_results->developer,
            'rating' => $json_results->rating,
            'release_date' => $json_results->rlsdate,
            'url' => $json_results->url,
        ];

        // Insert Data in Table
        \DB::table('games_metacritic')->where('id', $game->metacritic->id)->update($data_meta);

        // show a success message
        \Alert::success('<i class="fa fa-save m-r-5"></i> '.$game->name.' Metacritic data successfully refreshed!')->flash();

        return redirect(url($game->url_slug));
    }

    /**
     * Change giantbomb id.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws GuzzleException
     * @throws ModelException
     * @throws SdkException
     * @throws ValidationException
     */
    public function change_giantbomb(Request $request): RedirectResponse
    {
        // decrypt input
        $request->merge(['game_id' => decrypt($request->game_id)]);

        $this->validate($request, [
            'game_id' => 'required|exists:games,id',
        ]);

        $game = Game::with('listings')->find($request->game_id);

        // Check if game exists
        if (is_null($game)) {
            abort('404');
        }

        // Check if logged in
        if (! (auth()->check())) {
            return redirect(url('login'));
        }

        // Check if user can edit games
        if (! (auth()->user()->can('edit_games'))) {
            abort(403);
        }

        // Ignore user aborts and allow the script
        // to run forever
        ignore_user_abort(true);
        // set_time_limit(0);

        $apiKey = str_replace(' ', '', config('settings.giantbomb_key'));

        // Create a Config object and pass it to the Client
        $config = new Configuration($apiKey);
        $client = new \DBorsatto\GiantBomb\Client($config);

        // New Giantbomb ID
        $new_giantbomb_id = '3030-'.$request->giantbomb_id;

        // Check if giantbomb id is already in the database
        $giantbomb_check = Giantbomb::find($request->giantbomb_id);

        // Giantbomb ID is in database
        if ($giantbomb_check) {
            // get giantbomb data
            try {
                $giantbomb_game = $client->findWithResourceID('Game', $new_giantbomb_id);

                // Catch 404 error, when Giantbomb ID does not exist
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                // show a error message
                \Alert::error('<i class="fa fa-times m-r-5"></i> Sorry, this Giantbomb ID does not exists!')->flash();

                return redirect(url($game->url_slug));
            } catch (\Exception $e) {
                // show a error message
                \Alert::error('<i class="fa fa-times m-r-5"></i> Sorry, this Giantbomb ID could not be added!')->flash();

                return redirect(url($game->url_slug));
            }

            $images = $giantbomb_game->get('images');
            $cover_image = $giantbomb_game->get('image');

            // get genre from giantbomb
            if ($giantbomb_check->genres) {
                $giantbomb_genres = json_decode($giantbomb_check->genres);
                $db_genre = Genre::where('name', $giantbomb_genres[0])->first();
                if ($db_genre) {
                    $game->genre_id = $db_genre->id;
                } else {
                    if (config('settings.automatic_genres')) {
                        $new_genre = new Genre;
                        $new_genre->name = $giantbomb_genres[0]['name'];
                        $new_genre->save();
                        $game->genre_id = $new_genre->id;
                    }
                }
            }

            // Image Beta
            if (! $game->cover) {
                $extension = 'jpg';
                $newfilename = time().'-'.$game->id.'.'.$extension;
                $disk = 'local';
                $destination_path = 'public/games';

                $image_client = new Client();
                $image = $image_client->request('GET', $cover_image['super_url']);

                // 2. Store the image on disk.
                \Storage::disk($disk)->put($destination_path.'/'.$newfilename, $image->getBody()->getContents());

                $game->cover = $newfilename;
            }

            $giantbomb_check->image = substr($cover_image['icon_url'], 50);

            // get game with giantbomb id for tags and PEGI, when game exists
            $giantbomb_game = Game::where('giantbomb_id', $giantbomb_check->id)->first();
            if ($giantbomb_game) {
                // Tags add
                if ($giantbomb_game->tags) {
                    $game->tags = $giantbomb_game->tags;
                }

                // Pegi add
                if ($giantbomb_game->pegi) {
                    $game->pegi = $giantbomb_game->pegi;
                }
            }

            // Get data from giantbomb and save to new game
            $game->giantbomb_id = $giantbomb_check->id;
            $game->description = $giantbomb_check->summary;

            $game->save();

            // Image add
            $new_images = $this->getGiantBombImages(images: $images ?? []);

            $giantbomb_check->images = json_encode($new_images);
            $giantbomb_check->save();

            // Add new Giantbomb data to database
        } else {
            // get giantbomb data
            try {
                $giantbomb_game = $client->findWithResourceID('Game', $new_giantbomb_id);

                // Catch 404 error, when Giantbomb ID does not exist
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                // show a error message
                \Alert::error('<i class="fa fa-times m-r-5"></i> Sorry, this Giantbomb ID does not exists!')->flash();

                return redirect(url($game->url_slug));
            } catch (\Exception $e) {
                // show a error message
                \Alert::error('<i class="fa fa-times m-r-5"></i> Sorry, this Giantbomb ID could not be added!')->flash();

                return redirect(url($game->url_slug));
            }

            $images = $giantbomb_game->get('images');
            $cover_image = $giantbomb_game->get('image');
            $videos = $giantbomb_game->get('videos');

            // Get genres if exists
            try {
                $genres = $giantbomb_game->get('genres');
            } catch (\InvalidArgumentException $ex) {
                $genres = null;
            }

            $new_videos = [];

            if ($genres) {
                $new_genres = [];

                // Genres add
                foreach ($genres as $genre) {
                    array_push($new_genres, $genre['name']);
                    $check_genre = Genre::where('name', $genre['name'])->first();
                    if (! $check_genre) {
                        if (config('settings.automatic_genres')) {
                            $new_genre = new Genre;
                            $new_genre->name = $genre['name'];
                            $new_genre->save();
                            $game->genre_id = $new_genre->id;
                        }
                    } else {
                        $game->genre_id = $check_genre->id;
                    }
                }
            }

            // Image add
            $new_images = $this->getGiantBombImages(images: $images ?? []);

            // Video Add

            $video_help = 0;

            foreach ($videos as $video_api) {
                if ($video_help === 20) {
                    break;
                }

                if (! str_starts_with($video_api['name'], "Bombin' the A.M.")) {
                    try {
                        $video = $client->findWithResourceID('Video', $video_api['id']);

                        // We only want to save YouTube videos
                        if ($video->get('youtube_id') === '') {
                            continue;
                        }

                        $new_videos[$video_help]['name'] = $video_api['name'];
                        $new_videos[$video_help]['api_id'] = substr($video_api['api_detail_url'], 36, -1);

                        $new_videos[$video_help]['id'] = $video->get('id');
                        $new_videos[$video_help]['length_seconds'] = $video->get('length_seconds');
                        $new_videos[$video_help]['deck'] = $video->get('deck');
                        $new_videos[$video_help]['video_type'] = $video->get('video_type');
                        $new_videos[$video_help]['youtube_id'] = $video->get('youtube_id');

                        $video_image = $video->get('image');

                        $imageParts = explode('/', ($video_image['icon_url']));
                        $imageName = implode('/', array_slice($imageParts, -3, 3, true));

                        $new_videos[$video_help]['image'] = $imageName;

                        $video_help++;
                    } catch (\Exception $e) {
                        // catch code
                    }
                }
            }

            // PEGI Rating and get all ratings
            $ratings = $giantbomb_game->get('original_game_rating');

            $all_ratings = [];

            if ($ratings !== '') {
                $pegi = 0;

                foreach ($ratings ?? [] as $rating) {
                    // For array
                    array_push($all_ratings, $rating['name']);

                    // For database
                    $rating_name = substr($rating['name'], 0, 4);

                    if ($rating_name === 'PEGI') {
                        $pegi = substr($rating['name'], 6, -1);
                    }
                }

                if ($pegi !== 0) {
                    $game->pegi = $pegi;
                }
            }

            // Tags add
            if ($giantbomb_game->get('aliases') !== '') {
                $game->tags = $giantbomb_game->get('aliases');
            }

            // Data for SQL Insert
            $data = [
                'id' => $giantbomb_game->id,
                'name' => $giantbomb_game->name,
                'summary' => $giantbomb_game->deck,
                'genres' => $genres ? json_encode($new_genres) : null,
                'image' => substr($cover_image['icon_url'], 50),
                'images' => json_encode($new_images),
                'videos' => json_encode($new_videos),
                'ratings' => json_encode($all_ratings),
            ];

            // Insert Data in Table
            \DB::table('games_giantbomb')->insert($data);

            // Image Beta
            $extension = 'jpg';
            $newfilename = time().'-'.$game->id.'.'.$extension;
            $disk = 'local';
            $destination_path = 'public/games';

            $image_client = new Client();
            $image = $image_client->request('GET', $cover_image['super_url']);

            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path.'/'.$newfilename, $image->getBody()->getContents());

            // Delete old image
            if (! is_null($game->cover)) {
                \Storage::disk($disk)->delete('/public/games/'.$game->cover);
            }

            // Update Game Data with Giantbomb Info
            $game->giantbomb_id = $giantbomb_game->id;
            $game->cover = $newfilename;
            $game->description = $giantbomb_game->deck;
            $game->save();
        }

        // show a success message
        \Alert::success('<i class="fa fa-save m-r-5"></i> '.$game->name.' Giantbomb ID successfully changed!')->flash();

        return redirect(url($game->url_slug));
    }

    /**
     * Sort games.
     *
     * @param string $order
     * @param string|null $desc
     * @return string
     */
    public function order(string $order, string $desc = null): String
    {
        if ($order === 'release_date' || $order === 'metascore' || $order === 'listings' || $order === 'popularity') {
            session()->put('gamesOrder', $order);
        } else {
            session()->remove('gamesOrder');
        }

        session()->put('gamesOrderByDesc', $desc === 'desc');

        return redirect(url()->current() === url()->previous() ? url('/') : url()->previous());
    }

    /**
     * Prepares GiantBomb images to internal structure.
     */
    private function getGiantBombImages(array $images): array
    {
        $newImages = [];

        $imageCount = 0;
        foreach ($images ?? [] as $image) {
            $imageParts = explode('/', ($image['icon_url']));
            $imageName = implode('/', array_slice($imageParts, -3, 3, true));
            $newImages[$imageCount]['image'] = $imageName;
            $newImages[$imageCount]['tags'] = $image['tags'];
            $imageCount++;
        }

        return $newImages;
    }
}
