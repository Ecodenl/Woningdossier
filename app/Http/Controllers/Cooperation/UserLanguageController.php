<?php

namespace App\Http\Controllers\Cooperation;

use App\Models\Cooperation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserLanguageController extends Controller
{
    public function switchLanguage(Request $request, Cooperation $cooperation, $locale){
    	if (in_array($locale, config('woningdossier.supported_locales'))){
    		\Session::put('locale', $locale);
	    }

	    // redirect back
	    $prev = \Session::previousUrl();
    	if(!is_null($prev)){
    		return redirect($prev);
	    }
	    // try referer
	    $referer = $request->headers->get('referer', null);

    	if (is_null($referer)){
    		return redirect()->route('cooperation.welcome', compact('cooperation'));
	    }
	    else {
    		// check if referer is in current domain
		    $host = parse_url($referer, PHP_URL_HOST);
		    if (stristr($host, config('woningdossier.domain')) === false){
			    return redirect()->route('cooperation.welcome', compact('cooperation'));
		    }
		    return redirect($referer);
	    }
    }
}
