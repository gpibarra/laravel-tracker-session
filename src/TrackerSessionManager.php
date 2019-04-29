<?php

namespace gpibarra\TrackerSession;

use Illuminate\Http\Request;
use gpibarra\TrackerSession\Models\TrackerSession;
use gpibarra\TrackerSession\TrackerSessionServiceProvider;
use Illuminate\Support\Carbon;

class TrackerSessionManager
{

    public static function get(Request $request=null, $user=null) :?TrackerSession
    {
        if ($request==null) {
            $request = request();
        }
        if (app()->runningInConsole()) {
            return null;
        }
        $trackerSession = $request->session()->get(config('tracker-session.cookie_name_object'));
        // dd($trackerSession);
        if ($trackerSession == null) {
            $cookie = \Session::getId();

            $trackerSessionClass = TrackerSessionServiceProvider::determineSessionModel();
            $trackerSession = $trackerSessionClass::where('sessionKey', $cookie)->first();

            if ($trackerSession) {
                $trackerSession->see_at = Carbon::now();
            }
            else {
                $ip = $request->ip();
                $userAgent = $request->userAgent();

                $trackerSession = new $trackerSessionClass([
                    'type' => 'auth',
                    'sessionKey' => $cookie,
                    'ip_address' => $ip,
                    'user_agent' => $userAgent,
                ]);

                $trackerSession->see_at = Carbon::now();

            }
            if ($user) {
                $user->authentications()->save($trackerSession);
            }
            else {
                if (\Auth::user()) {
                    $user->authentications()->save($trackerSession);
                }
                else {
                    $trackerSession->save();
                }
            }
        }
        return $trackerSession;
    }

}
