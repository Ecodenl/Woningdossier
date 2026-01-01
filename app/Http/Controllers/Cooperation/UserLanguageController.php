<?php

namespace App\Http\Controllers\Cooperation;

use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use Illuminate\Http\Request;

class UserLanguageController extends Controller
{
    public function switchLanguage(Request $request, Cooperation $cooperation, $locale): RedirectResponse
    {
        if (in_array($locale, config('hoomdossier.supported_locales'))) {
            \Session::put('locale', $locale);
        }

        // redirect back
        $prev = \Session::previousUrl();
        if (! is_null($prev)) {
            return redirect($prev);
        }
        // try referer
        $referer = $request->headers->get('referer', null);

        if (is_null($referer)) {
            return to_route('cooperation.welcome', compact('cooperation'));
        } else {
            // check if referer is in current domain
            $host = parse_url($referer, PHP_URL_HOST);
            if (false === stristr($host, config('hoomdossier.domain'))) {
                return to_route('cooperation.welcome', compact('cooperation'));
            }

            return redirect($referer);
        }
    }
}
