<?php

namespace App\Providers;

use App\Macros\Collection\PullTranslationFromJson;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCollectionMacros();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
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
