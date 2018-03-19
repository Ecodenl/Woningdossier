<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class UserLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
	public function handle($request, Closure $next)
	{
		// Set the language from the URL segment (this allows us to later on
		// add a language switcher via URL for better front-site indexing).
		if(in_array($request->segment(1), config('woningdossier.supported_locales')))
		{
			Session::put('locale', $request->segment(1));
			//return Redirect::to(substr($request->path(), 3));
		}
		// If not entered via the URL, get the preferred language direct from
		// the request
		if(!Session::has('locale'))
		{
			\Log::debug("Session does not have locale");
			Session::put('locale', $request->getPreferredLanguage(config('woningdossier.supported_locales')));
		}

		// Check if the session has the language. If not, take the default
		// app.locale
		if(!Session::has('locale')) {
			\Log::debug("Sesssion does not have locale 2");
			Session::put('locale', config('app.locale'));
		}

		\Log::debug("Use locale: " . Session::get('locale'));
		app()->setLocale(Session::get('locale'));
		\Log::debug("App locale: " . app()->getLocale());

		return $next($request);
	}
}
