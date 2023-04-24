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
                $this->log($exception);
                if ($exception instanceof ServerException) {
                    // try again in 2 minutes
                    $this->release(120);
                }
            }, false);

        return;
    }

    private function log(\Throwable $exception)
    {
        Log::error(get_class($exception).' '.$exception->getCode().' '.$exception->getMessage());
        if (method_exists($exception, 'getResponse')) {
            /** @var Stream $stream */
            $stream = $exception->getResponse()->getBody();
            $stream->rewind();
            Log::error($stream->getContents());
        }

        $class = __CLASS__;
        DiscordNotifier::init()->notify(get_class($exception)." Failed to send '{$class}' building_id: {$this->building->id}");
    }
}