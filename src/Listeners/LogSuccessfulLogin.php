<?php

namespace gpibarra\TrackerSession\Listeners;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Auth\Events\Login;
use gpibarra\TrackerSession\TrackerSessionServiceProvider;
use gpibarra\TrackerSession\TrackerSessionManager;

class LogSuccessfulLogin
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
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;
        if ($user) {
            $ip = $this->request->ip();
            $userAgent = $this->request->userAgent();
            $cookie = \Session::getId();

            $trackerSession = new $this->trackerSessionClass([
                'type' => 'auth',
    //            'sessionKey' => $cookie,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);

            $trackerSession->login_at = Carbon::now();

            $user->authentications()->save($trackerSession);

            $this->request->session()->put(config('tracker-session.cookie_name_object'), $trackerSession);
            $this->request->session()->put(config('tracker-session.cookie_name_id'), $trackerSession->id);
            $this->request->session()->put(config('tracker-session.cookie_name_boolean_request_cookie_id'), true);
            $this->request->session()->save();

        }
    }
}
