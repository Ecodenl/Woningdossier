<?php

namespace App\Providers;

use App\Macros\Collection\PullTranslationFromJson;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerCollectionMacros();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Loading in all macros...
        // They are in a separate folder, so it's less clustered in here.
        foreach (glob(app_path('Macros/*.php')) as $filename) {
            require_once $filename;
        }
    }

    private function registerCollectionMacros()
    {
        Collection::make($this->collectionMacros())
                  ->reject(fn($class, $macro) => Collection::hasMacro($macro))
                  ->each(
                      fn($class, $macro) => Collection::macro(
                          $macro,
                          app($class)()
                      )
                  );
    }


    private function collectionMacros(): array
    {
        return [
            'pullTranslationFromJson' => PullTranslationFromJson::class,
        ];
    }
}
