<?php

namespace App\Http\Controllers\Cooperation;

use Illuminate\Http\RedirectResponse;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\InputSource;

class InputSourceController extends Controller
{
    public function changeInputSourceValue(Cooperation $cooperation, $inputSourceValueId): RedirectResponse
    {
        $inputSource = InputSource::find($inputSourceValueId);

        if ($inputSource instanceof InputSource) {
            HoomdossierSession::setInputSourceValue($inputSource);
        }

        return redirect()->back();
    }
}
