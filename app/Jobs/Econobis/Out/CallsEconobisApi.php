<?php

namespace App\Jobs\Econobis\Out;

use App\Helpers\Wrapper;
use App\Services\DiscordNotifier;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Predis\Response\ServerException;

trait CallsEconobisApi
{
    public function wrapCall(\Closure $function)
    {
        Wrapper::wrapCall(
            fn() => $function(),
            function (\Throwable $exception) {
                if ($exception instanceof ServerException) {
                    // try again in 2 minutes
                    $this->release(120);
                }

                // Econobis throws a 404 when something isnt right (could be a validation thing where a account id does not match the contact id)
                // anyway, this wont succeed in the next request, so we just fail the job.
                if ($exception instanceof RequestException) {
                    $class = __CLASS__;
                    DiscordNotifier::init()->notify("Failed to send '{$class}' building_id: {$this->building->id}");
                    Log::error($exception->getResponse()->getBody()->getContents());
                    // no point in trying again
                    $this->fail();
                }
            }, false);
    }
}