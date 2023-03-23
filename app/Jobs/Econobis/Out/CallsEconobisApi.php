<?php

namespace App\Jobs\Econobis\Out;

use App\Helpers\Wrapper;
use App\Models\Integration;
use App\Services\DiscordNotifier;
use App\Services\IntegrationProcessService;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Support\Facades\Log;
use Predis\Response\ServerException;

trait CallsEconobisApi
{
    public $tries = 3;

    public function wrapCall(\Closure $function)
    {
        Wrapper::wrapCall(
            function () use ($function) {
                $function();
                app(IntegrationProcessService::class)
                    ->forIntegration(Integration::findByShort('econobis'))
                    ->forBuilding($this->building)
                    ->forProcess(__CLASS__)
                    ->syncedNow();
            },
            function (\Throwable $exception) {
                if ($exception instanceof ServerException) {
                    // try again in 2 minutes
                    $this->release(120);
                }

                // Econobis throws a 404 when something isnt right (could be a validation thing where a account id does not match the contact id)
                // anyway, this wont succeed in the next request, so we just fail the job.
                if ($exception instanceof ClientException) {
                    $this->log($exception);
                } elseif ($exception instanceof TooManyRedirectsException) {
                    $this->log($exception);
                } elseif ($exception instanceof RequestException) {
                    $this->log($exception);

                }
            }, false);

        return;
    }

    private function log(\Throwable $exception)
    {
        /** @var Stream $stream */
        $stream = $exception->getResponse()->getBody();
        $stream->rewind();

        $class = __CLASS__;
        DiscordNotifier::init()->notify(get_class($exception)." Failed to send '{$class}' building_id: {$this->building->id}");

        Log::error(get_class($exception).' '.$exception->getCode().' '.$exception->getMessage());
        Log::error($stream->getContents());
    }
}