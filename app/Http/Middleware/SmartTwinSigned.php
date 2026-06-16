<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SmartTwinSigned
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('X-Webhook-ApiKey', '');
        $signKey = config('hoomdossier.services.smarttwin.sign-key', '');
        $previousSignKey = config('hoomdossier.services.smarttwin.previous-sign-key', '');

        $previousKeys = $previousSignKey !== ''
            ? array_map('trim', explode(',', $previousSignKey))
            : [];

        $validKeys = array_filter([$signKey, ...$previousKeys], fn($k) => $k !== '');

        if ($header !== '' && in_array($header, $validKeys, true)) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
