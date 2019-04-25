<?php

namespace gpibarra\TrackerSession\Middlewares;

use Closure;
use Illuminate\Http\Request;
use gpibarra\TrackerSession\TrackerSessionManager;

class TrackerSessionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (\Auth::check()) {
            if (!\Session::get(config('tracker-session.cookie_name_object'))) {
                $trackerSession = TrackerSessionManager::get($request, \Auth::user());
                \Session::put(config('tracker-session.cookie_name_object'), $trackerSession);
                \Session::save();
            }
        }
        if (\Session::get(config('tracker-session.cookie_name_boolean_request_cookie_id'))) {
            if ($trackerSession = TrackerSessionManager::get($request, \Auth::user())) {
                $cookie = \Session::getId();
                $trackerSession->sessionKey = $cookie;
                $trackerSession->save();
                \Session::put(config('tracker-session.cookie_name_object'), $trackerSession);
                \Session::forget(config('tracker-session.cookie_name_boolean_request_cookie_id'));
                \Session::save();
            }
        }
        return $next($request);
    }
}
