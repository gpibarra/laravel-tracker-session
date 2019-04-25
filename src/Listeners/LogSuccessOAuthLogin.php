<?php

namespace gpibarra\TrackerSession\Listeners;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Passport\Events\AccessTokenCreated;
use gpibarra\TrackerSession\TrackerSessionServiceProvider;
use gpibarra\TrackerSession\TrackerSessionManager;

class LogSuccessOAuthLogin
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
     * @param  AccessTokenCreated  $event
     * @return void
     */
    public function handle(AccessTokenCreated $event)
    {
        $user = \App\User::find($event->userId);
        if ($user) {
            $ip = $this->request->ip();
            $userAgent = "";
            $userAgent = $this->request->userAgent();
            if ($this->request->has('device_id')) {
                $userAgent .= " | Device:".$this->request->get('device_id').";";
            }
            if ($this->request->has('version')) {
                $userAgent .= " | Version:".$this->request->get('version').";";
            }

            $session = new $this->trackerSessionClass([
                'type' => 'oauth',
                'sessionKey' => $event->tokenId,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);

            $session->login_at = Carbon::now();

            $user->authentications()->save($session);

        }
    }
}
