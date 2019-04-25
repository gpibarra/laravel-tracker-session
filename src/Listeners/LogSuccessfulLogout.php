<?php

namespace gpibarra\TrackerSession\Listeners;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Auth\Events\Logout;
use gpibarra\TrackerSession\TrackerSessionServiceProvider;
use gpibarra\TrackerSession\TrackerSessionManager;

class LogSuccessfulLogout
{
    /**
     * The request.
     *
     * @var \Illuminate\Http\Request
     */
    public $request;

    /**
     * The trackerSession.
     *
     * @var string
     */
    public $trackerSessionClass;

    /**
     * Create the event listener.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->trackerSessionClass = TrackerSessionServiceProvider::determineSessionModel();
    }

    /**
     * Handle the event.
     *
     * @param  Logout  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        $user = $event->user;
        if ($user) {
            $trackerSession = null;
            $trackerSession = TrackerSessionManager::get($this->request, $user);

            if ($trackerSession) {
                $trackerSession->logout_at = Carbon::now();
                $trackerSession->save();
            }

            $this->request->session()->forget(config('tracker-session.cookie_name_id'));
            $this->request->session()->forget(config('tracker-session.cookie_name_object'));
            $this->request->session()->forget(config('tracker-session.cookie_name_boolean_request_cookie_id'));
            $this->request->session()->save();

        }
    }
}
