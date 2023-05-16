<?php

namespace App\Jobs\Econobis\Out;

use App\Helpers\Wrapper;
use App\Jobs\Middleware\EnsureCooperationHasEconobisLink;
use App\Models\Integration;
use App\Services\DiscordNotifier;
use App\Services\IntegrationProcessService;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Support\Facades\Cache;
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
        $shouldNotifyDiscord = false;

        $class = __CLASS__;
        $buildingId = $this->building->id ?? 'No building id!';

        if ($buildingId === 'No building id!') {
            $shouldNotifyDiscord = true;
        }

        // check whether this building id has failed before, if not we want to notify ourselfs.
        if (!in_array($buildingId, Cache::get('failed_econobis_building_ids'))) {
            $shouldNotifyDiscord = true;
        }

        // now save the building id to prevent a discord spam
        Cache::put(
            'failed_econobis_building_ids',
            array_unique(
                array_merge([$buildingId], Cache::get('failed_econobis_building_ids', []))
            )
        );

        if ($shouldNotifyDiscord) {
            DiscordNotifier::init()->notify(get_class($exception)." Failed to send '{$class}' building_id: {$buildingId}");
        }
    }

    public function middleware(): array
    {
        return [new EnsureCooperationHasEconobisLink($this->building->user->cooperation)];
    }
}