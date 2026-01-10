<?php

namespace App\Console\Commands\Api\Verbeterjehuis;

use App\Enums\MappingType;
use App\Helpers\Wrapper;
use App\Models\Municipality;
use App\Services\MappingService;
use App\Services\Verbeterjehuis\Verbeterjehuis;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RefreshRegulationsForCity extends Command
{
    protected $signature = 'api:verbeterjehuis:refresh-regulations-for-city
                            {municipality : The municipality name or ID}
                            {--dump : Dump the API response to console}';

    protected $description = 'Refresh the VerbeterJeHuis regulations cache for a specific municipality/city.';

    public function handle(): int
    {
        $municipalityInput = $this->argument('municipality');

        // Find municipality by ID or name
        $municipality = Municipality::where('id', $municipalityInput)
            ->orWhere('name', 'like', "%{$municipalityInput}%")
            ->first();

        if (! $municipality) {
            $this->error("Municipality '{$municipalityInput}' not found.");
            return self::FAILURE;
        }

        $this->info("Found municipality: {$municipality->name} (ID: {$municipality->id})");

        // Get the VerbeterJeHuis cityId mapping
        $target = MappingService::init()
            ->from($municipality)
            ->type(MappingType::MUNICIPALITY_VBJEHUIS->value)
            ->resolveTarget()
            ->first();

        $cityId = $target['Id'] ?? null;

        if (is_null($cityId)) {
            $this->error("No VerbeterJeHuis mapping found for municipality '{$municipality->name}'.");
            return self::FAILURE;
        }

        $this->info("VerbeterJeHuis cityId: {$cityId}");

        return Wrapper::wrapCall(function () use ($cityId) {
            // Calculate cache key (same logic as RegulationService::getCacheKey())
            $context = ['cityId' => $cityId];
            $cacheKey = md5(implode('|', $context));

            $this->info("Cache key: {$cacheKey}");

            // Remove old cache
            $cache = Cache::driver('database');
            $existed = $cache->has($cacheKey);
            $cache->forget($cacheKey);

            $this->info($existed ? "Old cache entry removed." : "No existing cache entry found.");

            // Fetch fresh data from API
            $this->info("Fetching regulations from VerbeterJeHuis API...");

            $regulations = app(Verbeterjehuis::class)
                ->regulation()
                ->search($context);

            $this->info("Fetched " . count($regulations) . " regulations.");

            // Store in cache (1 day, same as RegulationService)
            $cache->put($cacheKey, $regulations, Carbon::now()->addDay());

            $this->info("New data cached until: " . Carbon::now()->addDay()->format('Y-m-d H:i:s'));

            if ($this->option('dump')) {
                $this->newLine();
                $this->line(json_encode($regulations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }

            return self::SUCCESS;
        }, function ($exception) {
            $this->error("Failed to fetch regulations from VerbeterJeHuis: " . $exception->getMessage());
            return self::FAILURE;
        });
    }
}
