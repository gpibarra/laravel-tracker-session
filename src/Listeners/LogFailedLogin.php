<?php

namespace gpibarra\TrackerSession\Listeners;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Auth\Events\Failed;
use gpibarra\TrackerSession\TrackerSessionServiceProvider;
use gpibarra\TrackerSession\TrackerSessionManager;

class LogFailedLogin
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
     * @param  Failed  $event
     * @return void
     */
    public function handle(Failed $event)
    {
        $user = $event->user;
        if ($user) {
            $ip = $this->request->ip();
            $userAgent = $this->request->userAgent();

            $session = new $this->trackerSessionClass([
                'type' => 'failed_auth',
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);

            $session->failed_at = Carbon::now();

            $user->authentications()->save($session);
        }

    }
}
