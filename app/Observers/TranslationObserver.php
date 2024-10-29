<?php

namespace App\Observers;

use App\Models\Translation;

class TranslationObserver
{
    public function created(Translation $translation): void
    {
        // cache it
        \App\Helpers\Cache\Translation::getTranslations($translation);
    }

    public function updated(Translation $translation): void
    {
        // recache
        \App\Helpers\Cache\Translation::wipe($translation);
        \App\Helpers\Cache\Translation::getTranslations($translation);
    }

    public function deleted(Translation $translation): void
    {
        \App\Helpers\Cache\Translation::wipe($translation);
    }
}
