<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class UserLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->query('test') == "1") {
            Log::channel('single')->debug(__METHOD__);
        }
        if (app()->environment('local')) {
            // Set the language from the URL segment (this allows us to later on
            // add a language switcher via URL for better front-site indexing).
            if (in_array($request->segment(1),
                config('hoomdossier.supported_locales'))) {
                Session::put('locale', $request->segment(1));
                //return Redirect::to(substr($request->path(), 3));
            }
            // If not entered via the URL, get the preferred language direct from
            // the request
            if (! Session::has('locale')) {
                \Log::debug('Session does not have locale');
                Session::put('locale', $request->getPreferredLanguage(config('hoomdossier.supported_locales')));
            }

            // Check if the session has the language. If not, take the default
            // app.locale
            if (! Session::has('locale')) {
                Session::put('locale', config('app.locale'));
            }

            app()->setLocale(Session::get('locale'));
        } else {
            // until translations are done: force NL
            app()->setLocale('nl');
        }

        return $next($request);
    }
}
