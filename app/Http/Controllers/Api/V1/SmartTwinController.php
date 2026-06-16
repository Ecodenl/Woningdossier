<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class SmartTwinController
{
    public function store(Request $request): Response
    {
        Log::debug('SmartTwin webhook received', $request->json()->all());

        return response()->noContent();
    }
}
