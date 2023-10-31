<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\MessengerNew;
use Artesaos\SEOTools\Facades\SEOTools as SEO;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Prologue\Alerts\Facades\Alert;

class MessageController extends Controller
{
    /**
     * Show all the message threads to the user.
     *
     * @return View
     */
    public function index(): View
    {
        // All threads that user is participating in
        $threads = Thread::forUser(auth()->id())
                         ->where('offer_id', null)
                         ->with(['participants', 'users', 'messages', 'participants.user'])
                         ->latest('updated_at')
                         ->get();

        // SEO Page Title
        SEO::setTitle(trans('messenger.messenger').' - '.config('settings.page_name').' » '.config('settings.sub_title'));

        if ($threads->isEmpty()) {
            return view('frontend.messenger.no-threads');
        }

        return view('frontend.messenger.index', ['threads' => $threads]);
    }

    /**
     * Shows a message thread.
     *
     * @param int $id
     * @return RedirectResponse|View
     */
    public function show(int $id): RedirectResponse|View
    {
        // Check if request was sent through ajax
        if (! request()->ajax()) {
            return redirect()->route('messages');
        }

        try {
            $thread = Thread::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('messages');
        }

        $userId = auth()->id();

        // Check if user is participant of this thread
        $participant = $thread->participants->where('user_id', $userId)->first();
        if (! isset($participant)) {
            return redirect()->route('messages');
        }

        $messages = $thread->messages()->latest()->paginate(32);

        $thread->markAsRead($userId);

        return view('frontend.messenger.show', ['thread' => $thread, 'messages' => $messages]);
    }

    /**
     * Check for new messages in the thread.
     *
     * @param int $id
     * @return RedirectResponse|int
     */
    public function check(int $id): RedirectResponse|int
    {
        try {
            $thread = Thread::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            session()->flash('error_message', 'The thread with ID: '.$id.' was not found.');

            return redirect()->route('messages');
        }

        return $thread->userUnreadMessagesCount(auth()->id());
    }

    /**
     * Stores a new message thread.
     *
     * @return mixed
     */
    public function store(Request $request): RedirectResponse
    {
        $input = $request->all();

        // Check if message is empty
        if (strlen(trim($input['message'])) === 0) {
            // Show alert
            Alert::error('<i class="fa fa-times m-r-5"></i>'.trans('messenger.alert.no_input'))->flash();

            return back();
        }

        // Check if auth user is the recipient
        if (auth()->id() === $input['recipient']) {
            // Show alert
            Alert::error('<i class="fa fa-times m-r-5"></i>'.trans('messenger.alert.self_message'))->flash();

            return back();
        }

        // Check if recipient exists
        $recipient = User::find($input['recipient']);
        if (! isset($recipient)) {
            // Show alert
            Alert::error('<i class="fa fa-times m-r-5"></i>'.trans('messenger.alert.unkown_recipient'))->flash();

            return back();
        }

        // Check if thread already exists
        $thread = Thread::between([auth()->id(), $input['recipient']])->where('offer_id', null)->first();

        if (! isset($thread)) {
            $thread = Thread::create([
                'subject' => 'messenger',
            ]);
            // Sender
            Participant::create([
                'thread_id' => $thread->id,
                'user_id' => auth()->id(),
                'last_read' => new Carbon,
            ]);
            // Recipients
            if ($request->has('recipient')) {
                $thread->addParticipant($input['recipient']);
            }
        } else {
            // Check if latest message contains same text (spam protection)
            $latest_message = $thread->latest_message;
            if (isset($latest_message) && $latest_message->created_at->addSeconds(10) > now() && $latest_message->body === $request->input('message')) {
                // Show alert
                Alert::error('<i class="fa fa-times m-r-5"></i>'.trans('messenger.alert.duplicate_message'))->flash();

                return redirect()->route('messages');
            }
        }
        // Message
        Message::create([
            'thread_id' => $thread->id,
            'user_id' => auth()->id(),
            'body' => $input['message'],
        ]);

        // send notification to receiver
        $receiver_part = $thread->participants->where('user_id', '!=', auth()->id())->first();

        $receiver = User::find($receiver_part->user_id);

        $check_array = [
            'thread_id' => $thread->id,
            'user_id' => auth()->user()->id,
        ];

        // get the latest thread notification for the user
        $notification_check = $receiver->notifications()->where('data', json_encode($check_array))->first();

        if (! $notification_check || ! ($notification_check->created_at->addMinutes('60') > now())) {
            $receiver->notify(new MessengerNew($thread, auth()->user()));
        }

        return redirect()->route('messages');
    }

    /**
     * Adds a new message to a current thread.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse|int
     */
    public function update(Request $request, int $id): RedirectResponse|int
    {
        // Check if request was sent through ajax
        if (! request()->ajax()) {
            return redirect()->route('messages');
        }

        try {
            $thread = Thread::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            session()->flash('error_message', 'The thread with ID: '.$id.' was not found.');

            return redirect()->route('messages');
        }
        $thread->activateAllParticipants();

        // Check if message is empty
        if (strlen(trim($request->input('message'))) === 0) {
            abort(406, trans('messenger.alert.no_input'));
        }

        // Check if user is participant of this thread
        $participant = $thread->participants->where('user_id', auth()->id())->first();
        if (! isset($participant)) {
            return redirect()->route('messages');
        }

        // Check if latest message contains same text (spam protection)
        $latest_message = $thread->latest_message;
        if (isset($latest_message) && $latest_message->created_at->addSeconds(10) > now() && $latest_message->body === $request->input('message')) {
            abort(429, trans('messenger.alert.duplicate_message'));
        }

        // Message
        Message::create([
            'thread_id' => $thread->id,
            'user_id' => auth()->id(),
            'body' => $request->input('message'),
        ]);

        // Add replier as a participant
        $participant = Participant::firstOrCreate([
            'thread_id' => $thread->id,
            'user_id' => auth()->id(),
        ]);
        $participant->last_read = new Carbon;
        $participant->save();

        // send notification to receiver
        $receiver_part = $thread->participants->where('user_id', '!=', auth()->id())->first();

        $receiver = User::find($receiver_part->user_id);

        $check_array = [
            'thread_id' => $thread->id,
            'user_id' => auth()->user()->id,
        ];

        // get latest thread notification for the user
        $notification_check = $receiver->notifications()->where('data', json_encode($check_array))->first();

        if (! $notification_check || ! ($notification_check->created_at->addMinutes('60') > now())) {
            $receiver->notify(new MessengerNew($thread, auth()->user()));
        }

        return $id;
    }
}
