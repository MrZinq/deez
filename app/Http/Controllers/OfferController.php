<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\Payment;
use App\Models\Report;
use App\Models\Transaction;
use App\Models\User;
use App\Models\User_Rating;
use App\Notifications\MessageNew;
use App\Notifications\OfferDeleted;
use App\Notifications\OfferNew;
use App\Notifications\OfferStatus;
use App\Notifications\PaymentNew;
use App\Notifications\RatingNew;
use Artesaos\SEOTools\Facades\SEOTools as SEO;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Omnipay\Omnipay;
use Prologue\Alerts\Facades\Alert;

class OfferController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Offer overview.
     *
     * @param Offer $offer
     * @return View
     */
    public function show(Offer $offer): View
    {
        $listing = Listing::with('game', 'user', 'game.giantbomb', 'game.platform')->withTrashed()->find($offer->listing_id);

        // check if listing exist
        if (! $listing) {
            abort('404');
        }

        // Select User
        if (auth()->user()->id === $offer->user_id) {
            $user = $listing->user;
        } else {
            $user = $offer->user;
        }

        // Check if user is logged in
        if (! (auth()->user()->id === $offer->user_id || auth()->user()->id === $listing->user_id)) {
            // Check if offer reported and user is staff member
            if (! $offer->reported && ! auth()->user()->can('edit_offers')) {
                abort('404');
            }
        }

        SEO::setTitle(trans('general.title.offer', [
            'page_name'     => config('settings.page_name'),
            'platform'      => $listing->game->platform->name,
            'game_name'     => $listing->game->name,
            'user_name'     => $offer->user->name,
        ]));

        $trade_game = Game::find($offer->trade_game);

        $thread = Thread::findOrFail($offer->thread_id);

        return view('frontend.offer.show', [
            'offer'         => $offer,
            'listing'       => $listing,
            'game'          => $listing->game,
            'trade_game'    => $trade_game,
            'user'          => $user,
            'thread'        => $thread,
        ]);
    }

    /**
     * Add new offer.
     *
     * @param Request $request
     * @return RedirectResponse|View
     * @throws ValidationException
     */
    public function add(Request $request): RedirectResponse|View
    {
        // decrypt input
        // if ($request->trade_game) {
        //     $request->merge(array('game_id' => decrypt($request->game_id), 'listing_id' => decrypt($request->listing_id), 'trade_game' => decrypt($request->trade_game)));
        // } else {
        $request->merge(['game_id' => decrypt($request->game_id), 'listing_id' => decrypt($request->listing_id)]);
        // }

        $this->validate($request, [
            'game_id' => 'required|exists:games,id',
            'listing_id' => 'required|exists:listings,id',
            'trade_game' => 'exists:games,id',
        ]);

        $listing = Listing::find($request->listing_id);

        // Check if listing is available
        if ($listing->status === 1 || $listing->status === 2) {
            return redirect($listing->url_slug);
        }

        // Check if logged-in user wants to buy own listing
        if (auth()->user()->id === $listing->user_id) {
            Alert::error('<i class="fa fa-times m-r-5"></i>'.trans('offers.alert.own_offer'))->flash();

            return redirect($listing->url_slug);
        }

        // Check if listing user is not banned
        if (! $listing->user->isActive()) {
            return redirect('/');
        }

        // Check if user already send this offer and it's still active
        if ($listing->sell && ! $request->trade_game) {
            // Check if user have an same buy offer for this listing
            $check_offer = Offer::where('user_id', auth()->user()->id)
                                ->where('listing_id', $listing->id)
                                ->where('declined', 0)
                                ->where('price_offer', ($listing->sell_negotiate ? filter_var($request->price_suggestion, FILTER_SANITIZE_NUMBER_INT) : $listing->price))
                                ->where('delivery', ($request->delivery ? 1 : ($request->pickup ? 0 : ($listing->delivery && ! $listing->pickup ? 1 : 0))))
                                ->first();
        } else {
            // Check if user have an same trade offer for this listing
            $check_offer = Offer::where('user_id', auth()->user()->id)
                                ->where('listing_id', $listing->id)
                                ->where('declined', 0)
                                ->where('trade_game', $request->trade_game)
                                ->first();
        }

        // If this offer already exist, redirect to the offer page instead of creating a new offer
        if ($check_offer) {
            return redirect('/offer/'.$check_offer->id);
        }

        // Create new offer
        $offer = new Offer;

        // General data
        $offer->user_id = auth()->user()->id;
        $offer->listing_id = $listing->id;
        $offer->status = 0;

        // Delivery or Pickup
        // Check if user accept delivery and pickup
        if ($listing->delivery && $listing->pickup) {
            // User selected delivery
            if ($request->delivery) {
                $offer->delivery = 1;
            // User selected pickup
            } elseif ($request->pickup) {
                $offer->delivery = 0;
            }
            // User don't accept delivery and pickup
        } else {
            if ($listing->delivery && ! $listing->pickup) {
                $offer->delivery = 1;
            } elseif (! $listing->delivery && $listing->pickup) {
                $offer->delivery = 0;
            }
        }

        if ($listing->sell && ! $request->trade_game) {
            // Check if listing accept price suggestions
            if ($listing->sell_negotiate) {
                $offer->price_offer = $request->price_suggestion;
            } else {
                $offer->price_offer = $listing->getAttribute('price');
            }
        }

        if ($request->trade_game) {
            $trade_list = json_decode($listing->trade_list, true);
            // Check if selected game is from trade list
            if (! is_null($trade_list) && array_key_exists($request->trade_game, $trade_list) && ! $request->add_charge_user && ! $request->add_charge_partner) {
                $offer->trade_game = $request->trade_game;
                $offer->trade_from_list = 1;
                // save additional charge
                if ($trade_list[$request->trade_game]['price_type'] !== 'none') {
                    $offer->additional_type = $trade_list[$request->trade_game]['price_type'];
                    $offer->additional_charge = $trade_list[$request->trade_game]['price'];
                }
            } else {
                // check if accept game suggestions
                if ($listing->trade_negotiate) {
                    // Check if selected game is listing game
                    if ($request->trade_game === $listing->game->id) {
                        Alert::error('<i class="fa fa-times m-r-5"></i>'.trans('offers.alert.same_game'))->flash();

                        return redirect($listing->url_slug);
                    }
                    $offer->trade_game = $request->trade_game;
                    // game is not from trade list
                    $offer->trade_from_list = 0;

                    // additional_charge from partner (trade suggestions)
                    if ($request->add_charge_user && filter_var($request->add_charge_user, FILTER_SANITIZE_NUMBER_INT) > 0) {
                        $offer->additional_type = 'give';
                        // filter the float to int
                        $offer->additional_charge = abs(filter_var($request->add_charge_user, FILTER_SANITIZE_NUMBER_INT));
                    }

                    // additional_charge from partner (trade suggestions)
                    if ($request->add_charge_partner && filter_var($request->add_charge_partner, FILTER_SANITIZE_NUMBER_INT) > 0) {
                        $offer->additional_type = 'want';
                        // filter the float to int
                        $offer->additional_charge = abs(filter_var($request->add_charge_partner, FILTER_SANITIZE_NUMBER_INT));
                    }
                } else {
                    Alert::error('<i class="fa fa-times m-r-5"></i>'.trans('offers.alert.suggestion_disabled'))->flash();

                    return redirect($listing->url_slug);
                }
            }
        }

        if (! $offer->trade_game && empty($offer->price_offer)) {
            return redirect($listing->url_slug);
        }

        $offer->save();

        // Open Chat
        $thread = Thread::create(
            [
                'subject' => $listing->game->name.' offer from '.auth()->user()->name,
                'offer_id' => $offer->id,
            ]
        );

        if ($offer->trade_game) {
            // Message
            Message::create(
                [
                    'thread_id' => $thread->id,
                    'user_id'   => auth()->user()->id,
                    'body'      => trans('offers.general.chat_trade', [
                        'game_name'         => $listing->game->name,
                        'platform_name'     => $listing->game->platform->name,
                        'trade_game'        => $offer->game->name,
                        'Trade_platform'    => $offer->game->platform->name,
                    ]),
                ]
            );
        } else {
            // Message
            Message::create(
                [
                    'thread_id' => $thread->id,
                    'user_id'   => auth()->user()->id,
                    'body'      => trans('offers.general.chat_buy', [
                        'game_name'     => $listing->game->name,
                        'platform_name' => $listing->game->platform->name,
                        'price'         => $offer->price_offer_formatted,
                    ]),
                ]
            );
        }

        // Sender
        Participant::create(
            [
                'thread_id' => $thread->id,
                'user_id'   => auth()->user()->id,
                'last_read' => new Carbon,
            ]
        );

        // Recipients
        $thread->addParticipant($listing->user->id);

        $offer->thread_id = $thread->id;
        $offer->save();

        // add last offer timestamp on listing
        $listing->last_offer_at = new Carbon;
        $listing->save();

        // Send Notification to listing user
        $listing_user = User::find($listing->user_id);

        $listing_user->notify(new OfferNew($offer));

        return redirect('/offer/'.$offer->id);
    }

    /**
     * Delete offer.
     *
     * @param Request $request
     * @return RedirectResponse|View
     * @throws ValidationException
     */
    public function delete(Request $request): RedirectResponse
    {
        // decrypt input
        $request->merge(['offer_id' => decrypt($request->offer_id)]);

        $this->validate($request, [
            'offer_id' => 'required|exists:offers,id',
        ]);

        $offer = Offer::find($request->offer_id);

        if (! $offer) {
            abort('404');
        }

        // Check if logged in user can delete this offer
        if (! (auth()->user()->id === $offer->user_id)) {
            abort('404');
        }

        // Check status of listing
        if ($offer->status >= 1) {
            abort('404');
        }

        // Notifications to listing user
        if (($offer->listing->status === 0 || is_null($offer->listing->status)) && is_null($offer->listing->deleted_at)) {
            $listing_user = User::find($offer->listing->user_id);
            $listing_user->notify(new OfferDeleted($offer));
        }

        // Save reason for trash
        $offer->declined = 1;
        $offer->decline_note = 'offers.general.decline_reason_offer_deleted';
        $offer->closed_at = new Carbon;
        $offer->save();

        // show a success message
        Alert::error('<i class="fa fa-trash m-r-5"></i>'.trans('offers.alert.deleted', [
            'game_name' => str_replace("'", '', $offer->listing->game->name),
            ]))->flash();

        // delete offer
        $offer->delete();

        return redirect()->back();
    }

    /**
     * Rate user.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function rate(Request $request): RedirectResponse
    {
        // decrypt input
        $request->merge(['offer_id' => decrypt($request->offer_id)]);

        $this->validate($request, [
            'offer_id' => 'required|exists:offers,id',
        ]);

        $offer = Offer::find($request->offer_id);
        $listing = Listing::find($offer->listing_id);

        // Check if offer & listing exists
        if (! ($offer || $listing)) {
            return redirect('/');
        }

        // Check if logged user can review this offer
        if (! (auth()->user()->id === $offer->user_id || auth()->user()->id === $listing->user_id)) {
            return redirect('/');
        }

        // Check if review already added
        if (User_Rating::where('user_id_from', auth()->user()->id)->where('offer_id', $offer->id)->exists()) {
            return redirect('offer/'.$offer->id);
        }

        $rating = new User_Rating;

        // General data
        $rating->user_id_from = auth()->user()->id;
        $rating->user_id_to = auth()->user()->id === $offer->user_id ? $listing->user_id : $offer->user_id;
        $rating->rating = $request->review;
        $rating->notice = $request->review_note;

        $rating->offer_id = $offer->id;
        $rating->listing_id = $listing->id;

        $rating->save();

        if (auth()->user()->id === $offer->user_id) {
            // release money to seller
            if ($listing->payment && $offer->payment) {
                $this->transaction($offer->payment->id, $listing->user_id);
            }
            $offer->rating_id_offer = $rating->id;
            $offer->save();
        } elseif (auth()->user()->id === $listing->user_id) {
            $offer->rating_id_listing = $rating->id;
            $offer->save();
        }

        if (! is_null($offer->rating_id_listing) && ! is_null($offer->rating_id_offer)) {
            $rating_listing = User_Rating::find($offer->rating_id_listing);
            $rating_listing->active = 1;
            $rating_listing->save();
            $rating_offer = User_Rating::find($offer->rating_id_offer);
            $rating_offer->active = 1;
            $rating_offer->save();
            $listing->status = 2;
            $listing->save();
            $offer->status = 2;
            $offer->closed_at = now()->toDateTimeString();
            $offer->save();
            // send notifications to users
            $listing->user->notify(new RatingNew($offer, $rating_offer, $offer->user));
            $offer->user->notify(new RatingNew($offer, $rating_listing, $listing->user));
        }

        return redirect('/offer/'.$offer->id);
    }

    /**
     * Accept offer.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function accept(Request $request): RedirectResponse
    {
        // decrypt input
        $request->merge(['offer_id' => decrypt($request->offer_id)]);

        $this->validate($request, [
            'offer_id' => 'required|exists:offers,id',
        ]);

        $offer = Offer::find($request->offer_id);
        $listing = Listing::find($offer->listing_id);

        // check listing status
        if (! ($listing->status === 0 || is_null($listing->status))) {
            abort('404');
        }

        if ($offer->status !== 0) {
            abort('404');
        }

        // Check if logged user can accept this offer
        if (! (auth()->user()->id === $listing->user_id)) {
            return redirect('/');
        }

        $listing->status = 1;
        $listing->save();

        $offer->status = 1;
        $offer->save();

        // Remove all trade games
        \DB::table('game_trade')->where('listing_id', $listing->id)->delete();

        // Decline all other offers
        $offers = Offer::where('listing_id', $listing->id)->where('id', '!=', $offer->id)->where('declined', 0)->get();

        foreach ($offers as $doffer) {
            $doffer->declined = 1;
            $doffer->decline_note = 'offers.general.decline_reason_another_offer';
            $doffer->closed_at = new Carbon;
            $doffer->save();
            // Send notification to offer user
            $doffer->user->notify(new OfferStatus($doffer));
        }

        // Send notification to offer user
        $offer->user->notify(new OfferStatus($offer));

        return redirect('/offer/'.$offer->id);
    }

    /**
     * Decline offer.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function decline(Request $request): RedirectResponse
    {
        // decrypt input
        $request->merge(['offer_id' => decrypt($request->offer_id)]);

        $this->validate($request, [
            'offer_id' => 'required|exists:offers,id',
        ]);

        $offer = Offer::find($request->offer_id);
        $listing = Listing::find($offer->listing_id);

        // check listing status
        if (! ($listing->status === 0 || is_null($listing->status))) {
            abort('404');
        }

        if ($offer->status !== 0) {
            abort('404');
        }

        // Check if logged user can decline this offer
        if (! (auth()->user()->id === $listing->user_id)) {
            return redirect('/');
        }

        $offer->declined = 1;
        $offer->decline_note = $request->decline_note;
        $offer->closed_at = new Carbon;
        $offer->save();

        // Send notification to offer user
        $offer->user->notify(new OfferStatus($offer));

        return redirect('/offer/'.$offer->id);
    }

    /**
     * AJAX Chat for Offers Overview.
     *
     * @param int $id
     * @return View
     */
    public function chatOverview(int $id): View
    {
        $offer = Offer::withTrashed()->find($id);
        $listing = Listing::withTrashed()->with('user')->find($offer->listing_id);
        $thread = Thread::findOrFail($offer->thread_id);

        // Check if user has participant and mark message as read
        if ($thread->hasParticipant(auth()->user()->id)) {
            $thread->markAsRead(auth()->user()->id);
        }

        return view('frontend.offer.chat', ['offer' => $offer, 'listing' => $listing, 'thread' => $thread]);
    }

    /**
     * Add new message.
     *
     * @param Request $request
     * @return void
     * @throws ValidationException
     */
    public function newMessage(Request $request): void
    {
        // decrypt input
        $request->merge(['user_id' => decrypt($request->user_id), 'thread_id' => decrypt($request->thread_id)]);

        $this->validate($request, [
            'user_id'   => 'required|exists:users,id',
            'thread_id' => 'required|exists:messenger_threads,id',
        ]);

        $thread = Thread::findOrFail($request->thread_id);

        $offer = Offer::findOrFail($thread->offer_id);

        // Check if user is participant of thread
        if (! $thread->hasParticipant(auth()->user()->id)) {
            // Check if user is staff member
            if (! auth()->user()->can('edit_offers')) {
                abort('403');
            }
        }

        $check_array = [
            'listing_id'    => $offer->listing_id,
            'offer_id'      => $offer->id,
            'user_id'       => $request->user_id,
        ];

        $user = User::find($request->user_id);

        // notification all participants
        foreach ($thread->users as $participant) {
            if ($participant->id !== $request->user_id) {
                // get the latest thread notification for the user
                $notification_check = $participant->notifications()->where('data', json_encode($check_array))->first();
                if (! $notification_check || ! ($notification_check->created_at->addMinutes('45') > now())) {
                    $participant->notify(new MessageNew($offer, $user));
                }
            }
        }

        // Create new message
        Message::create([
          'thread_id' => $request->thread_id,
          'user_id'   => $request->user_id,
          'body'      => $request->message,
        ]);
    }

    /**
     * Report offer.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function report(Request $request): RedirectResponse
    {
        // decrypt input
        $request->merge(['offer_id' => decrypt($request->offer_id)]);

        $this->validate($request, [
            'offer_id' => 'required|exists:offers,id',
        ]);

        $offer = Offer::find($request->offer_id);
        $listing = Listing::find($offer->listing_id);

        // check offer status
        if ($offer->status === 0) {
            abort('404');
        }

        // Check if logged user can report this offer
        if (! (auth()->user()->id === $listing->user_id) && ! (auth()->user()->id === $offer->user_id)) {
            return redirect('/');
        }

        // Check if offer already reported
        if ($offer->reported) {
            // show a error message
            Alert::error('<i class="fa fa-life-ring m-r-5"></i>'.trans('offers.alert.already_reported', ['username' => $offer->report->user->name]))->flash();

            return redirect('/offer/'.$offer->id);
        }

        // Check if reason is set
        if (strlen($request->reason) === 0) {
            // show a error message
            Alert::error('<i class="fa fa-life-ring m-r-5"></i>'.trans('offers.alert.missing_reason'))->flash();

            return redirect('/offer/'.$offer->id);
        }

        // Create new report
        $report = new Report;
        // Report details
        $report->offer_id = $offer->id;
        $report->listing_id = $listing->id;
        $report->user_id = auth()->user()->id;
        $report->user_is = auth()->user()->id === $listing->user_id ? 'seller' : 'buyer';
        $report->reason = $request->reason;
        // Save report
        $report->save();

        // show a success message
        Alert::success('<i class="fa fa-life-ring m-r-5"></i>'.trans('offers.alert.reported'))->flash();

        return redirect('/offer/'.$offer->id);
    }

    /**
     * Ban User.
     *
     * @param int $id
     * @param int $user_id
     * @return RedirectResponse
     */
    public function reportBan(int $id, int $user_id): RedirectResponse
    {
        // Check if user can ban users
        if (! (auth()->user()->can('edit_offers'))) {
            return redirect('/');
        }

        // Get offer
        $offer = Offer::findOrFail($id);

        // Get user
        $banuser = User::findOrFail($user_id);

        // Check is user is participant of the offer
        if (! ($banuser->id === $offer->listing->user_id) && ! ($banuser->id === $offer->user_id)) {
            return redirect('/');
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

        return redirect('/offer/'.$offer->id);
    }

    /**
     * Close offer / listing.
     *
     * @param int $id , string $reopen
     * @param string|null $reopen
     * @return RedirectResponse
     */
    public function reportOfferClose(int $id, string $reopen = null): RedirectResponse
    {
        // Check if user can ban users
        if (! (auth()->user()->can('edit_offers'))) {
            return redirect('/');
        }

        // Get offer
        $offer = Offer::findOrFail($id);

        // Get Listing
        $listing = Listing::findOrFail($offer->listing->id);

        // Close or reopen listing
        if ($reopen === null) {
            $listing->status = 2;
            Alert::error('<i class="fa fa-tag m-r-5"></i> '.(! $offer->declined ? 'Offer &' : '').' Listing closed!')->flash();
        } else {

            // Add all trade games - first check if listing have a trade list
            if ($listing->trade_list) {
                foreach (json_decode($listing->trade_list) as $trade_game) {
                    $trade_synch_list[$trade_game->game_id] = ['listing_game_id' => $listing->game_id, 'price' => $trade_game->price, 'price_type' => $trade_game->price_type];
                }
                $listing->tradegames()->sync($trade_synch_list);
            }

            $listing->status = 0;
            Alert::success('<i class="fa fa-tag m-r-5"></i> '.(! $offer->declined ? 'Offer closed &' : '').' Listing reopened!')->flash();
        }

        $listing->save();

        if (! $offer->declined) {
            // Close offer
            $offer->declined = 1;
            $offer->decline_note = 'offers.general.decline_reason_staff';
            $offer->closed_at = new Carbon;
            // Save offer
            $offer->save();
        }

        return redirect('/offer/'.$offer->id);
    }

    /**
     * Close report.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function reportClose(int $id): RedirectResponse
    {
        // Check if user can ban users
        if (! (auth()->user()->can('edit_offers'))) {
            return redirect('/');
        }

        // Get offer
        $offer = Offer::findOrFail($id);

        // Check if offer is reported
        if ($offer->reported) {
            $report = $offer->report;

            $report->user_staff = auth()->user()->id;

            $report->status = $report->status ? 0 : 1;
            $report->closed_at = $report->status ? new Carbon : null;

            $report->save();
        }

        // show a success message
        if ($report->status) {
            Alert::success('<i class="fa fa-life-ring m-r-5"></i> Report closed!')->flash();
        } else {
            Alert::error('<i class="fa fa-life-ring m-r-5"></i> Report opened!')->flash();
        }

        return redirect('/offer/'.$offer->id);
    }

    /**
     * Close report.
     *
     * @param int $id
     * @param int $rating_id
     * @return RedirectResponse
     */
    public function reportRevoke(int $id, int $rating_id): RedirectResponse
    {
        // Check if user can ban users
        if (! (auth()->user()->can('edit_offers'))) {
            return redirect('/');
        }

        // Get offer
        $offer = Offer::findOrFail($id);

        $rating = User_Rating::findOrFail($rating_id);

        // Check if rating is from offer
        if (($rating->id !== $offer->rating_id_listing) && ($rating->id !== $offer->rating_id_offer)) {
            return redirect('/');
        }

        $rating->active = $rating->active ? 0 : 1;

        $rating->save();

        // show a success message
        if ($rating->active) {
            Alert::success('<i class="fa fa-repeat m-r-5"></i> Rating from '.$rating->user_from->name.' activated!')->flash();
        } else {
            Alert::error('<i class="fa fa-repeat m-r-5"></i> Rating from '.$rating->user_from->name.' revoked!')->flash();
        }

        return redirect('/offer/'.$offer->id);
    }

    /**
     * Show report.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function reportShow(int $id): RedirectResponse
    {
        // Check if user can ban users
        if (! (auth()->user()->can('edit_offers'))) {
            return redirect('/');
        }

        // Get offer
        $report = Report::findOrFail($id);

        return redirect('/offer/'.$report->offer->id);
    }

    /**
     * Show rating.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function ratingShow(int $id): RedirectResponse
    {
        // Check if user can ban users
        if (! (auth()->user()->can('edit_ratings'))) {
            return redirect('/');
        }

        // Get offer
        $rating = User_Rating::findOrFail($id);

        return redirect('/offer/'.$rating->offer->id);
    }

    /**
     * Post pay with available balance.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function payBalance(Request $request): RedirectResponse
    {
        // check if user account is active
        if (! auth()->user()->isActive()) {
            auth()->logout();

            return redirect('login')->with('error', trans('auth.deactivated'));
        }

        // decrypt input
        $request->merge(['offer_id' => decrypt($request->offer_id)]);

        $this->validate($request, [
            'offer_id' => 'required|exists:offers,id',
        ]);

        $offer = Offer::find($request->offer_id);
        $listing = Listing::find($offer->listing_id);

        // check offer status
        if ($offer->status === 0) {
            abort('404');
        }

        // check if user is offer user
        if (auth()->user()->id !== $offer->user_id) {
            Alert::error('<i class="fa fa-times m-r-5"></i> '.trans('payment.alert.canceled'))->flash();

            return redirect($offer->url);
        }

        // check if payment is possible
        if (! $offer->delivery || $offer->status !== 1 || ! $listing->payment) {
            Alert::error('<i class="fa fa-times m-r-5"></i> '.trans('payment.alert.canceled'))->flash();

            return redirect($offer->url);
        }

        // check if offer already paid
        if ($offer->payment && $offer->payment->status) {
            Alert::error('<i class="fa fa-times m-r-5"></i> '.trans('payment.alert.already_paid'))->flash();

            return redirect($offer->url);
        }

        $total = ($offer->price_offer + $listing->delivery_price) / 100;

        // Create new payment
        $payment = new Payment;

        // Offer details
        $payment->item_id = $offer->id;
        $payment->item_type = Offer::class;

        // Page User
        $payment->user_id = auth()->user()->id;

        // Transaction details from gateway
        $payment->transaction_id = auth()->user()->id.'-'.time();
        $payment->payment_method = 'balance';
        $payment->payer_info = json_encode(['email' => auth()->user()->email]);

        // Money
        $payment->total = $total;
        $payment->transaction_fee = '0';
        $payment->currency = config('settings.currency');

        // Save payment
        $payment->save();

        // purchase transaction
        $purchase_transaction = new Transaction;

        $purchase_transaction->type = 'purchase';
        $purchase_transaction->item_id = $offer->id;
        $purchase_transaction->item_type = Offer::class;
        $purchase_transaction->user_id = auth()->user()->id;
        $purchase_transaction->payment_id = $payment->id;
        $purchase_transaction->payer_id = $payment->user_id;
        $purchase_transaction->total = $total;
        $purchase_transaction->currency = config('settings.currency');

        $purchase_transaction->save();

        // remove sale total from user balance
        auth()->user()->balance -= $purchase_transaction->total;
        auth()->user()->save();

        // Send notification to seller
        $offer->listing->user->notify(new PaymentNew($offer, $payment));

        Alert::success('<i class="fa fa-check m-r-5"></i> '.trans('payment.alert.successful'))->flash();

        // show a success message
        Alert::success('<i class="fa fa-check m-r-5"></i> '.trans('payment.alert.successful'))->flash();

        return redirect('/offer/'.$offer->id);
    }

    /**
     * Post payment.
     *
     * @param Offer $offer
     * @return RedirectResponse|Exception
     */
    public function pay(Offer $offer): RedirectResponse|Exception
    {
        $listing = Listing::with('game', 'user', 'game.giantbomb', 'game.platform')->withTrashed()->find($offer->listing_id);

        // check if listing exist
        if (! $listing) {
            abort('404');
        }

        // check if user is offer user
        if (auth()->user()->id !== $offer->user_id) {
            Alert::error('<i class="fa fa-times m-r-5"></i> '.trans('payment.alert.canceled'))->flash();

            return redirect($offer->url);
        }

        // check if payment is possible
        if (! $offer->delivery || $offer->status !== 1 || ! $listing->payment) {
            Alert::error('<i class="fa fa-times m-r-5"></i> '.trans('payment.alert.canceled'))->flash();

            return redirect($offer->url);
        }

        // check if offer already paid
        if ($offer->payment && $offer->payment->status) {
            Alert::error('<i class="fa fa-times m-r-5"></i> '.trans('payment.alert.already_paid'))->flash();

            return redirect($offer->url);
        }

        $gateway = Omnipay::create('PayPal_Rest');

        // Initialise the gateway
        $gateway->initialize([
            'clientId' => config('settings.paypal_client_id'),
            'secret'   => config('settings.paypal_client_secret'),
            'testMode' => config('settings.paypal_sandbox'),
        ]);

        $items[] = [
            'name' => $listing->game->name.' ('.$listing->game->platform->name.')',
            'description' => trans('listings.general.condition').': '.$listing->condition_string.' - '.trans('payment.sold_by', [
                'username' => $listing->user->name,
                'country' => $listing->user->location->country_abbreviation,
                'place' => $listing->user->location->place,
                ]),
            'price' => $offer->price_offer / 100,
            'quantity' => '1',
        ];

        $params = [
            'cancelUrl' => url('offer/'.$offer->id.'/pay/cancel'),
            'returnUrl' => url('offer/'.$offer->id.'/pay/success'),
            'currency' =>config('settings.currency'),
            'shippingAmount' => (float) str_replace(',', '.', money($listing->delivery_price, config('settings.currency'))->format(false)),
            'amount' => (float) ($offer->price_offer + $listing->delivery_price) / 100,
        ];

        // Put params on the session
        Session::put('params', $params);
        Session::save();

        try {
            $response = $gateway->purchase($params)->setItems($items)->send();

            if ($response->isRedirect() && $response->isSuccessful()) {
                return $response->redirect();
            } else {
                //do something with an error
                return $response->getMessage();
            }
        } catch (Exception $e) {
            return $e;
        }
    }

    /**
     * Cancel payment.
     *
     * @param Offer $offer
     * @return RedirectResponse|View
     */
    public function payCancel(Offer $offer): RedirectResponse|View
    {
        if (Session::has('params')) {
            Alert::error('<i class="fa fa-times m-r-5"></i> '.trans('payment.alert.canceled'))->flash();
            Session::forget('params');
        }

        return $this->show($offer);
    }

    /**
     * Success payment.
     *
     * @param Offer $offer
     * @return RedirectResponse|View
     */
    public function paySuccess(Offer $offer): RedirectResponse|View
    {
        // Check for active payment session
        if (! Session::has('params')) {
            return $this->show($offer);
        }

        $gateway = Omnipay::create('PayPal_Rest');

        // Initialise the gateway
        $gateway->initialize([
            'clientId' => config('settings.paypal_client_id'),
            'secret'   => config('settings.paypal_client_secret'),
            'testMode' => config('settings.paypal_sandbox'),
        ]);

        $paymentId = $_GET['paymentId'];
        $payerId = $_GET['PayerID'];

        $response = $gateway->completePurchase([
            'payer_id'             => $payerId,
            'transactionReference' => $paymentId,
        ])->send()->getData();

        // check if payment is approved
        if ($response['state'] === 'approved') {
            $check_payment = Payment::where('transaction_id', $response['transactions']['0']['related_resources']['0']['sale']['id'])->first();

            // Check if a payment with this transaction is already in the database
            if ($check_payment === null) {
                // Create new payment
                $payment = new Payment;

                // Offer details
                $payment->item_id = $offer->id;
                $payment->item_type = Offer::class;

                // Page User
                $payment->user_id = auth()->user()->id;

                // Transaction details from gateway
                $payment->transaction_id = $response['transactions']['0']['related_resources']['0']['sale']['id'];
                $payment->payment_method = $response['payer']['payment_method'];
                $payment->payer_info = json_encode($response['payer']['payer_info']);

                // Money
                $payment->total = $response['transactions']['0']['amount']['total'];
                if (isset($response['transactions']['0']['related_resources']['0']['sale']['transaction_fee']['value'])) {
                    $payment->transaction_fee = $response['transactions']['0']['related_resources']['0']['sale']['transaction_fee']['value'];
                } else {
                    $payment->transaction_fee = 0;
                }

                $payment->currency = $response['transactions']['0']['amount']['currency'];

                // Save payment
                $payment->save();

                // Send notification to seller
                $offer->listing->user->notify(new PaymentNew($offer, $payment));
            }

            Alert::success('<i class="fa fa-check m-r-5"></i> '.trans('payment.alert.successful'))->flash();
        }

        Session::forget('params');

        return $this->show($offer);
    }

    /**
     * Stripe payment.
     *
     * @param Offer $offer
     * @param string $token
     * @return RedirectResponse|View
     */
    public function payStripe(Offer $offer, string $token): RedirectResponse|View
    {
        $listing = Listing::with('game', 'user', 'game.giantbomb', 'game.platform')->withTrashed()->find($offer->listing_id);

        // check if listing exist
        if (! $listing) {
            abort('404');
        }

        // check if user is offer user
        if (auth()->user()->id !== $offer->user_id) {
            Alert::error('<i class="fa fa-times m-r-5"></i> '.trans('payment.alert.canceled'))->flash();

            return $this->show($offer);
        }

        // check if offer already paid
        if ($offer->payment && $offer->payment->status) {
            Alert::error('<i class="fa fa-times m-r-5"></i> '.trans('payment.alert.already_paid'))->flash();

            return $this->show($offer);
        }

        $gateway = Omnipay::create('Stripe');

        // Initialise the gateway
        $gateway->initialize([
            'apiKey' => config('settings.stripe_client_secret'),
        ]);

        $response = $gateway->purchase([
            'amount' => str_replace(',', '.', money($offer->price_offer + $listing->delivery_price, config('settings.currency'))->format(false)),
            'currency' =>config('settings.currency'),
            'token' => $token,
            'expand' => ['balance_transaction'],
        ])->send();

        // check if payment is approved
        if ($response->isSuccessful()) {
            $data = $response->getData();

            $check_payment = Payment::where('transaction_id', $data['id'])->first();

            // Check if a payment with this transaction is already in the database
            if ($check_payment === null) {

                // Create new payment
                $payment = new Payment;

                // Offer details
                $payment->item_id = $offer->id;
                $payment->item_type = Offer::class;

                // Page User
                $payment->user_id = auth()->user()->id;

                // Transaction details from gateway
                $payment->transaction_id = $data['id'];
                $payment->payment_method = 'stripe';
                $payment->payer_info = json_encode($data['source']);

                // Money
                $payment->total = number_format($data['balance_transaction']['amount'] / 100, 2);
                $payment->transaction_fee = number_format($data['balance_transaction']['fee'] / 100, 2);
                $payment->currency = strtoupper($data['balance_transaction']['currency']);

                // Save payment
                $payment->save();
            }

            // Send notification to seller
            $offer->listing->user->notify(new PaymentNew($offer, $payment));

            Alert::success('<i class="fa fa-check m-r-5"></i> '.trans('payment.alert.successful'))->flash();
        } else {
            Alert::error('<i class="fa fa-times m-r-5"></i> '.trans('payment.alert.canceled'))->flash();
            Session::forget('params');
        }

        return redirect()->route('frontend.offer.show', ['offer' => $offer]);
    }

    /**
     * Refund payment.
     *
     * @param Offer $offer
     * @return RedirectResponse|View
     */
    public function payRefund(Offer $offer): RedirectResponse|View
    {
        $payment = Payment::where('item_type', Offer::class)->where('item_id', $offer->id)->where('status', '1')->first();

        // check if payment exists
        if (! $payment) {
            Alert::error('<i class="fa fa-times m-r-5"></i> This payment dont exists or is already refunded!')->flash();

            return redirect('offer/'.$offer->id);
        }

        // check if payment has transactions
        $transaction_check = Transaction::where('payment_id', $payment->id)->where('type', 'sale')->first();

        if ($transaction_check) {
            Alert::error('<i class="fa fa-times m-r-5"></i> Money already sent to the seller! Refund is not possible anymore.')->flash();

            return $this->show($offer);
        }

        // use the right payment gateway for the reunf
        // PayPal
        if ($payment->payment_method === 'paypal') {
            $gateway = Omnipay::create('PayPal_Rest');

            // Initialise the gateway
            $gateway->initialize([
                'clientId' => config('settings.paypal_client_id'),
                'secret'   => config('settings.paypal_client_secret'),
                'testMode' => config('settings.paypal_sandbox'),
            ]);

            $response = $gateway->refund()->setTransactionReference($payment->transaction_id)->send();
        // Stripe
        } elseif ($payment->payment_method === 'stripe') {
            $gateway = Omnipay::create('Stripe');

            // Initialise the gateway
            $gateway->initialize([
                'apiKey' => config('settings.stripe_client_secret'),
            ]);

            $response = $gateway->refund([
                'amount'               => $payment->total,
                'transactionReference' => $payment->transaction_id,
            ])->send();
        } elseif ($payment->payment_method === 'balance') {
            // purchase transaction
            $refund_transaction = new Transaction;

            $refund_transaction->type = 'refund';
            $refund_transaction->item_id = $payment->item_id;
            $refund_transaction->item_type = $payment->item_type;
            $refund_transaction->user_id = $payment->user->id;
            $refund_transaction->payment_id = $payment->id;
            $refund_transaction->payer_id = $payment->user->id;
            $refund_transaction->total = $payment->total;
            $refund_transaction->currency = $payment->currency;

            $refund_transaction->save();

            // remove sale total from user balance
            $payment->user->balance += $refund_transaction->total;
            $payment->user->save();
        }

        // check if payment is approved
        if ((isset($response) && $response->isSuccessful()) || $payment->payment_method === 'balance') {
            $payment->status = 0;
            $payment->save();
            Alert::success('<i class="fa fa-check m-r-5"></i> '.trans('payment.alert.refunded'))->flash();
        }

        return redirect('offer/'.$offer->id);
    }

    /**
     * Refund payment.
     *
     * @param Offer $offer
     * @return mixed
     */
    public function payRelease(Offer $offer): RedirectResponse
    {
        $payment = Payment::where('item_type', Offer::class)->where('item_id', $offer->id)->where('status', '1')->first();

        // check if payment exists
        if (! $payment) {
            Alert::error('<i class="fa fa-times m-r-5"></i> This payment dont exists or is already refunded!')->flash();

            return redirect('offer/'.$offer->id);
        }

        // release money to seller
        if ($offer->payment) {
            if ($this->transaction($offer->payment->id, $offer->listing->user_id)) {
                Alert::success('<i class="fa fa-check m-r-5"></i> Money released to seller!')->flash();
            }
        }

        return redirect('offer/'.$offer->id);
    }

    /**
     * Transaction to seller.
     *
     * @param int $id
     * @param int $user_id
     * @return mixed
     */
    public function transaction(int $id, int $user_id): bool
    {
        // get payment
        $payment = Payment::find($id);

        // check if payment exist or is deleted
        if (! $payment) {
            return false;
        }

        // check if payment was not refunded
        if (! $payment->status) {
            return false;
        }

        // get user
        $user = User::find($user_id);

        // check if user exist
        if (! $user) {
            return false;
        }

        // check if transaction already exist
        $sale_transaction_check = Transaction::where('item_id', $payment->item_id)
                                             ->where('item_type', $payment->item_type)
                                             ->where('type', 'sale')
                                             ->first();

        if ($sale_transaction_check) {
            return false;
        }

        $fee = round($payment->total * (config('settings.variable_fee') / 100), 2) + config('settings.fixed_fee');

        // sale transaction
        $sale_transaction = new Transaction;

        $sale_transaction->type = 'sale';
        $sale_transaction->item_id = $payment->item_id;
        $sale_transaction->item_type = $payment->item_type;
        $sale_transaction->user_id = $user->id;
        $sale_transaction->payment_id = $payment->id;
        $sale_transaction->payer_id = $payment->user_id;
        $sale_transaction->total = $payment->total - $fee;
        $sale_transaction->currency = $payment->currency;

        $sale_transaction->save();

        // add sale total to user balance
        $user->balance += $sale_transaction->total;
        $user->save();

        // fee transaction
        $fee_transaction = new Transaction;

        $fee_transaction->type = 'fee';
        $fee_transaction->item_id = $payment->item_id;
        $fee_transaction->item_type = $payment->item_type;
        $fee_transaction->user_id = $user_id;
        $fee_transaction->payment_id = $payment->id;
        $fee_transaction->payer_id = $payment->user_id;
        $fee_transaction->total = $fee;
        $fee_transaction->currency = $payment->currency;

        $fee_transaction->save();

        return true;
    }
}
