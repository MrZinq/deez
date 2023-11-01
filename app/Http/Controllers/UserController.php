<?php

namespace App\Http\Controllers;

use App\Http\Requests\Frontend\User\ChangePasswordRequest;
use App\Http\Requests\Frontend\User\UpdateProfileRequest;
use App\Http\Requests\WithdrawalRequest;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use App\Repositories\UserRepository;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Prologue\Alerts\Facades\Alert;
use Wiledia\Searchy\Facades\Searchy;

class UserController
{
    /**
     * UserController constructor.
     *
     * @param UserRepository $user
     */
    public function __construct(protected UserRepository $user)
    {
    }

    /**
     * Settings form.
     *
     * @return View
     */
    public function settingsForm(): View
    {
        // Page title
        SEOMeta::setTitle(trans('users.dash.settings.settings').' - '.config('settings.page_name'));

        return view('frontend.user.settings.profile', ['user' => auth()->user(), 'location' => auth()->user()->location]);
    }

    /**
     * Save settings.
     *
     * @param UpdateProfileRequest $request
     * @return RedirectResponse
     */
    public function settingsSave(UpdateProfileRequest $request): RedirectResponse
    {
        $this->user->updateProfile(auth()->id(), $request);

        return redirect()->route('dashboard.settings');
    }

    /**
     * Password form.
     *
     * @return view
     */
    public function passwordForm(): View
    {
        // Page title
        SEOMeta::setTitle(trans('users.dash.settings.password_heading').' - '.config('settings.page_name'));

        return view('frontend.user.settings.password', ['user' => auth()->user()]);
    }

    /**
     * Change password.
     *
     * @param ChangePasswordRequest $request
     * @return RedirectResponse
     */
    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $this->user->changePassword($request->all());

        return redirect()->to('dash/settings/password');
    }

    /**
     * User profile.
     *
     * @param string $slug
     * @return view
     */
    public function show(String $slug): View
    {
        // Get user from slug string
        $user = User::with('listings', 'listings.game', 'listings.game.platform', 'listings.user', 'listings.game.giantbomb', 'location')
                    ->where('name', $slug)
                    ->first();

        // Check if user exists
        if (is_null($user)) {
            abort(404);
        }

        // Page title
        SEOMeta::setTitle(trans('general.title.profile', [
            'page_name' => config('settings.page_name'),
            'sub_title' => config('settings.sub_title'),
            'user_name' => $user->name,
        ]));

        // Get image size for og
        if ($user->avatar) {
            $imgsize = getimagesize($user->avatar_square);
            OpenGraph::addImage(['url' => $user->avatar_square, ['height' => $imgsize[1], 'width' => $imgsize[0]]]);
        }

        // Page description
        SEOMeta::setDescription(trans('general.description.profile', [
            'user_name'         => $user->name,
            'listings_count'    => $user->listings->count(),
            'page_name'         => config('settings.page_name'),
            'sub_title'         => config('settings.sub_title'),
        ]));

        return view('frontend.user.show', [
            'user'      => $user,
            'ratings'   => $user->ratings()->with('user_from')->get(),
            'listings'  => $user
                ->listings()
                ->where('user_id', $user->id)
                ->where('status', 0)
                ->orWhere('status', null)
                ->where('user_id', $user->id)
                ->with('game', 'game.platform', 'user')
                ->orderByDesc('created_at')
                ->paginate(36),
        ]);
    }

    /**
     * Notifications dashboard.
     *
     * @return view
     */
    public function notifications(): View
    {
        // Page title
        SEOMeta::setTitle(trans('notifications.title').' - '.config('settings.page_name'));

        return view('frontend.user.dash.notifications', ['user' => auth()->user()]);
    }

    /**
     * Notifications api.
     *
     * @return view
     */
    public function notificationsApi(): View
    {
        return view('frontend.user.api.notifications', ['user' => auth()->user()]);
    }

    /**
     * Mark notification as read.
     *
     * @param  Request $request
     * @return array
     */
    public function notificationsRead(Request $request): array
    {
        $notification = auth()->user()->notifications()->findOrFail($request->notif_id);
        $notification->markAsRead();

        return [
            'success' => true,
            'message' => 'Notification read',
        ];
    }

    /**
     * Mark all notification as read.
     *
     * @return RedirectResponse
     */
    public function notificationsReadAll(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        // show a success message
        Alert::success('<i class="fa fa-check m-r-5"></i>'.trans('notifications.mark_all_read_alert'))->flash();

        return redirect()->back();
    }

    /**
     * Save user location.
     *
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function locationSave(Request $request): ResponseFactory|Response
    {
        if ($this->user->updateLocation(auth()->id(), $request)) {
            return response(['msg' => 'Location saved'], 200);
        }

        return response(['msg' => 'Error, location not saved!'], 422);
    }

    /**
     * User dashboard.
     *
     * @param Request $request
     * @return RedirectResponse|View
     */
    public function dashboard(Request $request): RedirectResponse|View
    {
        // Page title
        SEOMeta::setTitle(trans('users.dash.dashboard').' - '.config('settings.page_name'));

        // Save back URL for finished form
        session()->flash('backUrl', $request->fullUrl());

        // Check if logged in
        if (! (auth()->check())) {
            return redirect('/');
        }

        $user = User::with('listings', 'listings.game', 'listings.game.platform', 'listings.offers', 'listings.offers.game', 'listings.offers.user', 'offers', 'offers.listing')
                    ->where('id', auth()->user()->id)
                    ->first();

        return view('frontend.user.dash.overview', ['user' => $user]);
    }

    /**
     * Listings dashboard.
     *
     * @param Request $request
     * @param string|null $sort
     * @return view
     */
    public function listings(Request $request, string $sort = null): View
    {
        // Page title
        SEOMeta::setTitle(trans('general.listings').' - '.config('settings.page_name'));

        // Save back URL for finished form
        session()->flash('backUrl', $request->fullUrl());

        // Check for right link, otherwise abort and send 404
        if (! ($sort === null) && ! ($sort === 'complete') && ! ($sort === 'deleted')) {
            abort('404');
        }

        $user = User::with('listings')->where('id', auth()->user()->id)->first();

        $listings_trashed_count = Listing::onlyTrashed()
                                         ->where('user_id', $user->id)
                                         ->where('deleted_at', '!=', null)
                                         ->with('game', 'game.platform', 'offers', 'offers.game', 'offers.user', 'offers.user.location')
                                         ->orderBy('deleted_at', 'desc')
                                         ->count();

        if ($sort === 'complete') {
            $listings = Listing::where('user_id', $user->id)
                               ->where('status', 2)
                               ->with('game', 'game.platform', 'offers', 'offers.game', 'offers.user', 'offers.user.location')
                               ->orderBy('updated_at', 'desc')
                               ->paginate('10');
        } elseif ($sort === 'deleted') {
            $listings = Listing::onlyTrashed()
                               ->where('user_id', $user->id)
                               ->where('deleted_at', '!=', null)
                               ->with('game', 'game.platform', 'offers', 'offers.game', 'offers.user', 'offers.user.location')
                               ->orderBy('deleted_at', 'desc')
                               ->paginate('10');
        } else {
            $listings = Listing::where('user_id', $user->id)
                               ->where('status', null)
                               ->orWhere('status', 0)
                               ->where('user_id', $user->id)
                               ->orWhere('status', 1)
                               ->where('user_id', $user->id)
                               ->with('game', 'game.platform', 'offers', 'offers.game', 'offers.user', 'offers.user.location')
                               ->orderBy('last_offer_at', 'desc')
                               ->paginate('10');
        }

        return view('frontend.user.dash.listings', ['user' => $user, 'listings' => $listings, 'listings_trashed_count' => $listings_trashed_count]);
    }

    /**
     * Offers dashboard.
     *
     * @param  Request $request
     * @param string|null $sort
     * @return view
     */
    public function offers(Request $request, string $sort = null): View
    {

        // Page title
        SEOMeta::setTitle(trans('general.offers').' - '.config('settings.page_name'));

        // Save back URL for finished form
        session()->flash('backUrl', $request->fullUrl());

        // Check for right link, otherwise abort and send 404
        if (! ($sort === null) && ! ($sort === 'complete') && ! ($sort === 'declined') && ! ($sort === 'deleted')) {
            abort('404');
        }

        $user = auth()->user();

        $offers_trashed_count = Offer::onlyTrashed()
                                     ->where('user_id', $user->id)
                                     ->with('game', 'listing', 'listing.game', 'listing.game.platform', 'listing.user', 'listing.user.location')
                                     ->orderBy('deleted_at', 'desc')
                                     ->count();

        if ($sort === 'complete') {
            $offers = Offer::where('user_id', $user->id)
                           ->where('status', 2)
                           ->with('game', 'listing', 'listing.game', 'listing.game.platform', 'listing.user', 'listing.user.location')
                           ->orderBy('closed_at', 'desc')
                           ->paginate('10');
        } elseif ($sort === 'declined') {
            $offers = Offer::where('user_id', $user->id)
                           ->where('declined', 1)
                           ->with('game', 'listing', 'listing.game', 'listing.game.platform', 'listing.user', 'listing.user.location')
                           ->orderBy('closed_at', 'desc')
                           ->paginate('10');
        } elseif ($sort === 'deleted') {
            $offers = Offer::onlyTrashed()
                           ->where('user_id', $user->id)
                           ->with('game', 'listing', 'listing.game', 'listing.game.platform', 'listing.user', 'listing.user.location')
                           ->orderBy('deleted_at', 'desc')
                           ->paginate('10');
        } else {
            $offers = Offer::where('user_id', $user->id)
                           ->where('status', null)
                           ->where('declined', 0)
                           ->orWhere('status', 0)
                           ->where('user_id', $user->id)
                           ->where('declined', 0)
                           ->orWhere('status', 1)
                           ->where('user_id', $user->id)
                           ->where('declined', 0)
                           ->with('game', 'listing', 'listing.game', 'listing.game.platform', 'listing.user', 'listing.user.location')
                           ->orderBy('updated_at', 'desc')
                           ->paginate('10');
        }

        return view('frontend.user.dash.offers', ['user' => $user, 'offers' => $offers, 'offers_trashed_count' => $offers_trashed_count]);
    }

    /**
     * Ban User.
     *
     * @param int $user_id
     * @return mixed
     */
    public function ban(int $user_id): RedirectResponse
    {
        // Check if user can ban users
        if (! (auth()->user()->can('edit_users'))) {
            abort(404);
        }
        // Get user
        $banuser = User::findOrFail($user_id);

        // Check if admin / mod will selfban
        if (auth()->user()->id === $banuser->id) {
            Alert::error('<i class="fa fa-user-times m-r-5"></i> You cant ban yourself!')->flash();

            return redirect()->back();
        }

        // Ban / Unban User
        $banuser->status = $banuser->status ? '0' : '1';
        $banuser->save();

        // show a success message
        if ($banuser->status) {
            Alert::success('<i class="fa fa-user-times m-r-5"></i> '.$banuser->name.' succesfully unbaned')->flash();
        } else {
            Alert::error('<i class="fa fa-user-times m-r-5"></i> '.$banuser->name.' succesfully baned')->flash();
        }

        return redirect()->back();
    }

    /**
     * Save geolocation from guest.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function guestGeoLocation(Request $request): string
    {
        session()->put('latitude', $request->get('latitude'));
        session()->put('longitude', $request->get('longitude'));

        return 'saved';
    }

    /**
     * Balance dashboard.
     *
     * @return view
     */
    public function balance(): View
    {
        // Page title
        SEOMeta::setTitle(trans('payment.transactions').' - '.config('settings.page_name'));

        $transactions = Transaction::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->paginate('12');

        $sale_count = Transaction::where('user_id', auth()->user()->id)->where('type', 'sale')->count();

        return view('frontend.user.dash.balance', ['transactions' => $transactions, 'sale_count' => $sale_count]);
    }

    /**
     * Withdrawal dashboard.
     *
     * @return view
     */
    public function withdrawal(): RedirectResponse|View
    {
        // Page title
        SEOMeta::setTitle(trans('payment.withdrawal.withdrawal').' - '.config('settings.page_name'));

        // check if user has available balance
        if (auth()->user()->balance <= 0) {
            Alert::error('<i class="fa fa-times m-r-5"></i> '.trans('payment.withdrawal.alert.no_balance').'')->flash();

            return redirect('dash/balance');
        }

        $transactions = Transaction::where('user_id', auth()->user()->id)->orderBy('id', 'desc')->get();

        $withdrawal = Withdrawal::where('user_id', auth()->user()->id)->where('status', '1')->paginate('12');

        return view('frontend.user.dash.withdrawal', ['withdrawal' => $withdrawal, 'transactions' => $transactions]);
    }

    /**
     * Withdrawal dashboard.
     *
     * @param WithdrawalRequest $request
     * @param string|null $method
     * @return RedirectResponse|View
     */
    public function addWithdrawal(WithdrawalRequest $request, string $method = null): RedirectResponse|View
    {
        if (! isset($method) || isset($method) && ! ($method === 'paypal' || $method === 'bank')) {
            Alert::error('<i class="fa fa-user-times m-r-5"></i> '.trans('payment.withdrawal.alert.failed').'')->flash();

            return redirect()->back();
        } else {
            // Check if PayPal is allowed
            if ($method === 'paypal' && ! config('settings.withdrawal_paypal')) {
                Alert::error('<i class="fa fa-user-times m-r-5"></i> '.trans('payment.withdrawal.alert.failed').'')->flash();

                return redirect()->back();
            }

            // Check if Bank Transfer is allowed
            if ($method === 'bank' && ! config('settings.withdrawal_bank')) {
                Alert::error('<i class="fa fa-user-times m-r-5"></i> '.trans('payment.withdrawal.alert.failed').'')->flash();

                return redirect()->back();
            }

            $user = auth()->user();

            // check if user have available balance
            if ($user->balance <= 0) {
                Alert::error('<i class="fa fa-times m-r-5"></i> '.trans('payment.withdrawal.alert.no_balance').'')->flash();

                return redirect('dash/balance');
            }

            $withdrawal = new Withdrawal;

            $withdrawal->user_id = $user->id;
            if ($method === 'paypal') {
                $withdrawal->payment_method = 'paypal';
                $withdrawal->payment_details = $request->paypal_email;
            }

            if ($method === 'bank') {
                $bank = [
                    'holder_name' => $request->bank_holder_name,
                    'iban' => $request->bank_iban,
                    'bic' => $request->bank_bic,
                    'bank_name' => $request->bank_name,
                ];
                $withdrawal->payment_method = 'bank';
                $withdrawal->payment_details = json_encode($bank);
            }
            $withdrawal->currency = config('settings.currency');
            $withdrawal->total = $user->balance;

            $withdrawal->save();

            // remove balance from user account
            $user->balance = 0.00;
            $user->save();

            // sale transaction
            $withdrawal_transaction = new Transaction;

            $withdrawal_transaction->type = 'withdrawal';
            $withdrawal_transaction->item_id = $withdrawal->id;
            $withdrawal_transaction->item_type = get_class($withdrawal);
            $withdrawal_transaction->user_id = $user->id;
            $withdrawal_transaction->total = $withdrawal->total;
            $withdrawal_transaction->currency = $withdrawal->currency;

            $withdrawal_transaction->save();

            Alert::success('<i class="fa fa-check m-r-5"></i> '.trans('payment.withdrawal.alert.successfully').'')->flash();

            return redirect('dash/balance');
        }
    }

    /**
     * Save player id from user for web push notifications.
     *
     * @param  string $func
     */
    public function push(string $func, Request $request): RedirectResponse|string
    {
        $user = auth()->user();

        // Subscribe user and add player id
        if ($func === 'add') {
            // Check if player id already exist
            $player_check = \DB::table('user_player_ids')->where('player_id', $request->player_id)->first();
            // Add new player id to database
            if (! $player_check) {
                \DB::table('user_player_ids')->insert(
                    ['user_id' => $user->id, 'player_id' => $request->player_id]
                );
            }

            return 'player id saved';

        // Unsubscribe user and remove player id
        } elseif ($func === 'remove') {
            \DB::table('user_player_ids')->where('player_id', $request->player_id)->delete();

            return 'player id removed';
        }

        return 'error';
    }

    /**
     * Search with json response.
     *
     * @param  string  $value
     * @return JsonResponse
     */
    public function searchJson(string $value): JsonResponse
    {
        // Check if request was sent through ajax
        if (! request()->ajax()) {
            abort('404');
        }

        $users = User::hydrate(
            Searchy::users('name')
                                      ->query($value)
                                      ->getQuery()
                                      ->where('id', '!=', auth()->user()->id)
                                      ->limit(10)
                                      ->get()
                                      ->toArray()
        );

        $data = [];

        foreach ($users as $user) {
            $data[' '.$user->id]['id'] = $user->id;
            $data[' '.$user->id]['name'] = $user->name;
            $data[' '.$user->id]['avatar'] = $user->avatar_square_tiny;
            $data[' '.$user->id]['status'] = $user->isOnline() ? 'online' : 'offline';
        }

        // and return to typeahead
        return response()->json($data);
    }
}
