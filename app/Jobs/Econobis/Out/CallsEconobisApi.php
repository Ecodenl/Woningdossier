<?php

namespace App\Jobs\Econobis\Out;

use App\Helpers\Hoomdossier;
use App\Helpers\Str;
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
        if (Hoomdossier::hasEnabledEconobisCalls()) {
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
                },
                false
            );
        } else {
            $buildingId = $this->building->id ?? 'No building id!';
            Log::debug('Building ' . $buildingId . ' - Econobis calls are disabled, skipping call');
        }
    }

    private function log(\Throwable $exception)
    {
        $class = __CLASS__;
        $buildingId = $this->building->id ?? 'No building id!';

        Log::error(sprintf('Building %s - %s %s %s', $buildingId, get_class($exception), $exception->getCode(), $exception->getMessage()));
        if (method_exists($exception, 'getResponse')) {
            /** @var Stream $stream */
            $stream = $exception->getResponse()->getBody();
            $stream->rewind();

            $contents = $stream->getContents();
            Log::error($contents);

            if (Str::of($contents)->contains('ErrorException')) {
                report($exception);
            }
        }

        $shouldNotifyDiscord = false;

        if ($buildingId === 'No building id!') {
            $shouldNotifyDiscord = true;
        }

        // Check whether this building ID has failed before, if not we want to notify ourselves.
        if (! in_array($buildingId, Cache::get('failed_econobis_building_ids', []))) {
            $shouldNotifyDiscord = true;
        }

        // Now save the building id to prevent a discord spam
        Cache::put(
            'failed_econobis_building_ids',
            array_unique(
                array_merge([$buildingId], Cache::get('failed_econobis_building_ids', []))
            )
        );

        if ($shouldNotifyDiscord) {
            $environment = app()->environment();
            DiscordNotifier::init()->notify(get_class($exception)." Failed to send [{$environment}] '{$class}' building_id: {$buildingId}");
        }
    }

    public function middleware(): array
    {
        return [new EnsureCooperationHasEconobisLink($this->building->user->cooperation)];
    }
}
